<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBioRequest;
use App\Http\Requests\UpdateBioRequest;
use App\Models\Bio;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

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
        $tags = Tag::orderBy('name')->get();
        return view('bios.create',compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBioRequest $request)
    {
        //
        $validated = $request->validated();
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);
        DB::transaction(function () use ($validated,$tags) {
            $bio = Bio::create([
                ...$validated,
                'user_id' => auth()->id(),
            ]);
            $bio->tags()->sync($tags);
        });
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
        $tags = Tag::orderBy('name')->get();
        return view('bios.edit', compact('bio','tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBioRequest $request, Bio $bio)
    {
        //
        $this->authorize('update', $bio);

        $validated = $request->validated();
        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);
        DB::transaction(function () use ($validated,$tags,$bio) {
            $bio->update($validated);
            $bio->tags()->sync($tags);
        });
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
