<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes every state-changing web request idempotent.
 *
 * A request is identified by the acting user (or guest session), the HTTP method,
 * the path and a hash of the submitted payload. Traditional form submissions also
 * carry a per-render key (added by public/js/form-guard.js) which extends how long
 * a completed submission is remembered, so the back button or a refresh-resubmit
 * replays the original outcome instead of repeating the action.
 *
 * While the original request is running, duplicates wait for it and then replay its
 * result. Failed requests (validation errors, 4xx/5xx) are never remembered, so the
 * user can correct the form and submit again.
 */
class EnsureIdempotentSubmission
{
    private const FLASH_KEYS = ['success', 'error', 'warning', 'info', 'status', 'message'];
    private const MAX_JSON_BYTES = 262144;
    private const MAX_HASHED_FILE_BYTES = 10485760;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->shouldGuard($request)) {
            return $next($request);
        }

        try {
            [$id, $ttl] = $this->identify($request);
        } catch (\Throwable $e) {
            Log::warning('Idempotency fingerprint failed; request processed unguarded.', ['error' => $e->getMessage()]);
            return $next($request);
        }

        $lockKey = "idempotency:lock:{$id}";
        $resultKey = "idempotency:result:{$id}";

        try {
            $acquired = $this->acquire($lockKey, $resultKey);
        } catch (\Throwable $e) {
            Log::warning('Idempotency store unavailable; request processed unguarded.', ['error' => $e->getMessage()]);
            return $next($request);
        }

        if (is_array($acquired)) {
            return $this->replay($request, $acquired);
        }

        if ($acquired === false) {
            return $this->stillProcessing($request);
        }

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $this->forget($lockKey);
            throw $e;
        }

        try {
            $snapshot = $this->snapshot($request, $response);
            if ($snapshot !== null) {
                Cache::put($resultKey, $snapshot, $ttl);
            }
        } catch (\Throwable $e) {
            Log::warning('Idempotency result could not be stored.', ['error' => $e->getMessage()]);
        } finally {
            $this->forget($lockKey);
        }

        return $response;
    }

    private function shouldGuard(Request $request): bool
    {
        if (!config('idempotency.enabled', true) || $request->isMethodSafe() || !$request->hasSession()) {
            return false;
        }

        foreach ((array) config('idempotency.except_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        $routeName = $request->route()?->getName();
        if ($routeName) {
            foreach ((array) config('idempotency.except_routes', []) as $pattern) {
                if (\Illuminate\Support\Str::is($pattern, $routeName)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function identify(Request $request): array
    {
        $actor = $request->user() ? 'user:' . $request->user()->getAuthIdentifier() : 'session:' . $request->session()->getId();
        $parts = [$actor, $request->method(), $request->path(), $this->payloadHash($request)];

        $key = $this->clientKey($request);
        // AJAX callers often submit the same rendered form several times on purpose,
        // so only full-page form submissions get the long, key-based memory.
        if ($key !== null && !$request->ajax() && !$request->expectsJson()) {
            $parts[] = 'key:' . $key;
            $ttl = (int) config('idempotency.key_ttl', 3600);
        } else {
            $ttl = (int) config('idempotency.fingerprint_ttl', 3);
        }

        return [hash('sha256', implode('|', $parts)), max(1, $ttl)];
    }

    private function clientKey(Request $request): ?string
    {
        $key = $request->header(config('idempotency.header', 'X-Idempotency-Key'))
            ?? $request->input(config('idempotency.input_key', '_idempotency_key'));

        if (!is_string($key)) {
            return null;
        }

        $key = trim($key);

        return ($key !== '' && strlen($key) <= 100 && preg_match('/^[A-Za-z0-9._:-]+$/', $key)) ? $key : null;
    }

    private function payloadHash(Request $request): string
    {
        $input = $request->except(['_token', config('idempotency.input_key', '_idempotency_key')]);
        $files = $this->describeFiles($request->allFiles());

        return hash('sha256', json_encode([$this->normalize($input), $files], JSON_PARTIAL_OUTPUT_ON_ERROR));
    }

    private function describeFiles(array $files): array
    {
        $described = [];
        foreach ($files as $name => $file) {
            if (is_array($file)) {
                $described[$name] = $this->describeFiles($file);
            } elseif ($file instanceof UploadedFile) {
                $path = $file->getRealPath();
                $size = (int) $file->getSize();
                $described[$name] = [
                    $file->getClientOriginalName(),
                    $size,
                    ($path && $size <= self::MAX_HASHED_FILE_BYTES) ? md5_file($path) : null,
                ];
            }
        }
        ksort($described);

        return $described;
    }

    private function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            if (!array_is_list($value)) {
                ksort($value);
            }
            return array_map(fn ($item) => $this->normalize($item), $value);
        }

        return $value;
    }

    /**
     * @return bool|array true when the lock is ours, false on timeout, array to replay a stored result
     */
    private function acquire(string $lockKey, string $resultKey): bool|array
    {
        $deadline = microtime(true) + (float) config('idempotency.wait_seconds', 10);

        do {
            $stored = Cache::get($resultKey);
            if (is_array($stored)) {
                return $stored;
            }

            if (Cache::add($lockKey, 1, (int) config('idempotency.lock_ttl', 120))) {
                // The original may have finished between our two cache reads.
                $stored = Cache::get($resultKey);
                if (is_array($stored)) {
                    $this->forget($lockKey);
                    return $stored;
                }
                return true;
            }

            usleep(250000);
        } while (microtime(true) < $deadline);

        return false;
    }

    private function snapshot(Request $request, Response $response): ?array
    {
        $status = $response->getStatusCode();
        if ($status >= 400 || $this->hasValidationErrors($request)) {
            return null;
        }

        if ($response instanceof RedirectResponse) {
            return [
                'type' => 'redirect',
                'status' => $status,
                'location' => $response->getTargetUrl(),
                'flash' => $this->capturedFlash($request),
            ];
        }

        $contentType = (string) $response->headers->get('Content-Type');
        if ($response instanceof JsonResponse || str_contains($contentType, 'json')) {
            $content = (string) $response->getContent();
            if (strlen($content) <= self::MAX_JSON_BYTES) {
                return [
                    'type' => 'json',
                    'status' => $status,
                    'content' => $content,
                    'content_type' => $contentType ?: 'application/json',
                ];
            }
        }

        // Rendered pages and downloads are not replayable; only the in-flight lock applied.
        return null;
    }

    private function hasValidationErrors(Request $request): bool
    {
        return in_array('errors', (array) $request->session()->get('_flash.new', []), true);
    }

    private function capturedFlash(Request $request): array
    {
        $session = $request->session();
        $fresh = (array) $session->get('_flash.new', []);
        $flash = [];
        foreach (self::FLASH_KEYS as $key) {
            if (in_array($key, $fresh, true) && is_scalar($session->get($key))) {
                $flash[$key] = $session->get($key);
            }
        }

        return $flash;
    }

    private function replay(Request $request, array $snapshot): Response
    {
        $notice = 'This submission was already received and processed, so it was not submitted again.';

        if (($snapshot['type'] ?? null) === 'json') {
            return response($snapshot['content'], $snapshot['status'])
                ->header('Content-Type', $snapshot['content_type'])
                ->header('X-Idempotent-Replay', 'true');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $notice, 'replayed' => true, 'redirect' => $snapshot['location'] ?? null], 200)
                ->header('X-Idempotent-Replay', 'true');
        }

        foreach ((array) ($snapshot['flash'] ?? []) as $key => $value) {
            $request->session()->flash($key, $value);
        }
        $request->session()->flash('info', $notice);

        return redirect()->to($snapshot['location'] ?? url()->previous(), $snapshot['status'] ?? 302)
            ->header('X-Idempotent-Replay', 'true');
    }

    private function stillProcessing(Request $request): Response
    {
        $message = 'Your previous submission is still being processed. Please wait a moment before trying again.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 409)->header('Retry-After', '5');
        }

        return redirect()->back()->with('warning', $message);
    }

    private function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Throwable) {
            // Lock expires on its own via lock_ttl.
        }
    }
}
