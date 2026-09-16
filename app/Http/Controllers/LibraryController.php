<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $stats = [
            'catalogue_items' => LibraryItem::where('is_active', true)->count(),
            'available_copies' => LibraryItem::where('is_active', true)->sum('available_copies'),
            'fines_due' => 0,
        ];

        return view('library.index', compact('stats'));
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
            ->paginate(15)
            ->withQueryString();

        return view('library.catalogue', compact('items'));
    }

    public function loansIndex()
    {
        return view('library.loans');
    }

    public function myLoans()
    {
        return view('library.my-loans');
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
        ]);
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
        ]);
        $item->update($data);
        $this->notify('Library catalogue item updated', "{$item->title} was updated.", 'library.catalogue.index');

        return redirect()->route('library.catalogue.index')->with('success', 'Catalogue item updated successfully.');
    }

    public function destroy(LibraryItem $item)
    {
        $title = $item->title;
        $item->delete();
        $this->notify('Library catalogue item removed', "{$title} was removed from the catalogue.", 'library.catalogue.index');

        return redirect()->route('library.catalogue.index')->with('success', 'Catalogue item deleted successfully.');
    }

    private function notify(string $title, string $message, string $route): void
    {
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'library',
            'title' => $title,
            'message' => $message,
            'action_url' => route($route),
            'priority' => 'normal',
        ]);
    }
}
