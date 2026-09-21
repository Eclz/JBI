<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\LibraryLoan;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $stats = [
            'catalogue_items' => LibraryItem::count(),
            'active_loans' => LibraryLoan::where('status', 'borrowed')->count(),
            'overdue_loans' => LibraryLoan::where('status', 'overdue')->count(),
            'fines_due' => LibraryLoan::sum('fines'),
        ];
        
        $recentActivity = LibraryLoan::with(['libraryItem', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('library.index', compact('stats', 'recentActivity'));
    }

    public function catalogueIndex(Request $request)
    {
        $items = LibraryItem::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('library.catalogue', compact('items'));
    }

    public function show(LibraryItem $item)
    {
        return view('library.show', compact('item'));
    }

    public function loansIndex()
    {
        $loans = LibraryLoan::with(['libraryItem', 'user'])->latest()->paginate(15);
        return view('library.loans.index', compact('loans'));
    }

    public function createLoan()
    {
        $items = LibraryItem::where('is_active', true)->where('available_copies', '>', 0)->get();
        $users = User::all();
        return view('library.loans.create', compact('items', 'users'));
    }

    public function storeLoan(Request $request)
    {
        $user = Auth::user();
        $isLibrarianOrAdmin = !$user->isStudent();

        if (!$isLibrarianOrAdmin) {
            $request->merge([
                'user_id' => $user->id,
                'borrowed_at' => now()->format('Y-m-d'),
                'due_at' => now()->addDays(14)->format('Y-m-d'),
            ]);
        }

        $data = $request->validate([
            'library_item_id' => 'required|exists:library_items,id',
            'user_id' => 'required|exists:users,id',
            'borrowed_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:borrowed_at',
        ]);

        $item = LibraryItem::findOrFail($data['library_item_id']);
        if ($item->available_copies <= 0) {
            return back()->withInput()->with('error', 'No copies available for this item.');
        }

        $data['status'] = 'borrowed';
        $loan = LibraryLoan::create($data);
        
        $item->decrement('available_copies');

        $this->notify('Book borrowed', "You borrowed {$item->title}.", 'library.my-loans', $loan->user_id);

        if (!$isLibrarianOrAdmin) {
            return redirect()->route('library.my-loans')->with('success', 'You have successfully borrowed this item.');
        }

        return redirect()->route('library.loans.index')->with('success', 'Loan issued successfully.');
    }

    public function showLoan(LibraryLoan $loan)
    {
        $loan->load(['libraryItem', 'user']);
        return view('library.loans.show', compact('loan'));
    }

    public function editLoan(LibraryLoan $loan)
    {
        $items = LibraryItem::where('is_active', true)->get();
        $users = User::all();
        return view('library.loans.edit', compact('loan', 'items', 'users'));
    }

    public function updateLoan(Request $request, LibraryLoan $loan)
    {
        $data = $request->validate([
            'borrowed_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:borrowed_at',
            'returned_at' => 'nullable|date',
            'status' => 'required|in:borrowed,returned,overdue',
            'fines' => 'required|numeric|min:0',
        ]);

        $loan->update($data);

        return redirect()->route('library.loans.index')->with('success', 'Loan updated successfully.');
    }

    public function renewLoan(LibraryLoan $loan)
    {
        if ($loan->status !== 'borrowed' && $loan->status !== 'overdue') {
            return back()->with('error', 'Only active loans can be renewed.');
        }

        $loan->increment('renewals');
        $loan->update(['due_at' => now()->addDays(14)]);

        return back()->with('success', 'Loan renewed successfully for 14 days.');
    }

    public function returnLoan(Request $request, LibraryLoan $loan)
    {
        if ($loan->status === 'returned') {
            return back()->with('error', 'Loan is already returned.');
        }

        $loan->update([
            'status' => 'returned',
            'returned_at' => now(),
            'fines' => $request->input('fines', $loan->fines),
        ]);

        $loan->libraryItem->increment('available_copies');

        return back()->with('success', 'Item returned successfully.');
    }

    public function myLoans()
    {
        $loans = LibraryLoan::where('user_id', Auth::id())->with('libraryItem')->latest()->paginate(15);
        return view('library.loans.my-loans', compact('loans'));
    }

    public function create()
    {
        return view('library.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:32|unique:library_items,isbn',
            'category' => 'required|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('library/covers', 'public');
        }
        $data['available_copies'] = $data['total_copies'];
        $item = LibraryItem::create($data);
        $this->notify('Library catalogue item created', "{$item->title} was added to the catalogue.", 'library.catalogue.index');

        return redirect()->route('library.catalogue.index')->with('success', 'Catalogue item created successfully.');
    }

    public function edit(LibraryItem $item)
    {
        return view('library.edit', compact('item'));
    }

    public function update(Request $request, LibraryItem $item)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:32|unique:library_items,isbn,' . $item->id,
            'category' => 'required|string|max:100',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'is_active' => 'required|boolean',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($item->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('library/covers', 'public');
        }
        $item->update($data);
        $this->notify('Library catalogue item updated', "{$item->title} was updated.", 'library.catalogue.index');

        return redirect()->route('library.catalogue.index')->with('success', 'Catalogue item updated successfully.');
    }

    public function destroy(LibraryItem $item)
    {
        $title = $item->title;
        if ($item->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item->cover_image);
        }
        $item->delete();
        $this->notify('Library catalogue item removed', "{$title} was removed from the catalogue.", 'library.catalogue.index');

        return redirect()->route('library.catalogue.index')->with('success', 'Catalogue item deleted successfully.');
    }

    private function notify(string $title, string $message, string $route, ?int $userId = null): void
    {
        Notification::create([
            'user_id' => $userId ?? Auth::id(),
            'type' => 'library',
            'title' => $title,
            'message' => $message,
            'action_url' => route($route),
            'priority' => 'normal',
        ]);
    }
}
