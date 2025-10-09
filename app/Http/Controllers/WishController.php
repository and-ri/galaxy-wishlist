<?php

namespace App\Http\Controllers;

use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WishController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wishes = Auth::user()->wishes()->latest()->get();
        return view('wishes.index', compact('wishes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wishes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'priority' => 'nullable|integer|min:0|max:2',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_path' => 'nullable|string', // For images downloaded from URL
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('wishes', 'public');
        } elseif ($request->has('image_path') && $request->image_path) {
            // Use the already downloaded image
            $validated['image'] = $request->image_path;
        }

        // Remove image_path from validated data as it's not a database field
        unset($validated['image_path']);

        Auth::user()->wishes()->create($validated);

        return redirect()->route('wishes.index')->with('success', 'Бажання успішно додано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wish $wish)
    {
        return view('wishes.show', compact('wish'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wish $wish)
    {
        $this->authorize('update', $wish);
        return view('wishes.edit', compact('wish'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wish $wish)
    {
        $this->authorize('update', $wish);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'priority' => 'nullable|integer|min:0|max:2',
            'is_purchased' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_path' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($wish->image) {
                Storage::disk('public')->delete($wish->image);
            }
            $validated['image'] = $request->file('image')->store('wishes', 'public');
        } elseif ($request->has('image_path') && $request->image_path) {
            // Delete old image if using new downloaded image
            if ($wish->image && $wish->image !== $request->image_path) {
                Storage::disk('public')->delete($wish->image);
            }
            $validated['image'] = $request->image_path;
        }

        // Remove image_path from validated data
        unset($validated['image_path']);

        $wish->update($validated);

        return redirect()->route('wishes.index')->with('success', 'Бажання оновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wish $wish)
    {
        $this->authorize('delete', $wish);
        
        // Delete associated image
        if ($wish->image) {
            Storage::disk('public')->delete($wish->image);
        }
        
        $wish->delete();

        return redirect()->route('wishes.index')->with('success', 'Бажання видалено!');
    }
}
