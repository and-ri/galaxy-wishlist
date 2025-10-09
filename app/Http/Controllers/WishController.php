<?php

namespace App\Http\Controllers;

use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishController extends Controller
{
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
        ]);

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
        ]);

        $wish->update($validated);

        return redirect()->route('wishes.index')->with('success', 'Бажання оновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wish $wish)
    {
        $this->authorize('delete', $wish);
        
        $wish->delete();

        return redirect()->route('wishes.index')->with('success', 'Бажання видалено!');
    }
}
