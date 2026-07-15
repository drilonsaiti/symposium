<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBioRequest;
use App\Http\Requests\UpdateBioRequest;
use App\Models\Bio;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BioController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $bios = auth()->user()->bios()->paginate(10);
        return view('bios.index', compact('bios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('bios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBioRequest $request)
    {
        //
        $validated = $request->validated();
        Bio::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('bios.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bio $bio)
    {
        //
        $this->authorize('view', $bio);
        return view('bios.show', compact('bio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bio $bio)
    {
        //
        $this->authorize('update', $bio);
        return view('bios.edit', compact('bio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBioRequest $request, Bio $bio)
    {
        //
        $this->authorize('update', $bio);

        $validated = $request->validated();
        $bio->update($validated);
        return redirect()->route('bios.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bio $bio)
    {
        //
        $this->authorize('delete', $bio);

        $bio->delete();
        return redirect()->route('bios.index');
    }
}
