<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sources = Source::latest()->paginate(10);

        return view('admin.sources.index', compact('sources'));
    }

    /**
     * Show the form for creating a new resource.
     * Not used - modal is used instead.
     */
    public function create()
    {
        throw new NotFoundHttpException;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'collector_key' => ['required', 'string', 'max:100', 'unique:sources,collector_key'],
            'active' => ['boolean'],
        ]);

        Source::create($validated);

        return redirect()->route('admin.sources.index')->with('status', 'Source créée avec succès.');
    }

    /**
     * Display the specified resource.
     * Not used - modal is used instead.
     */
    public function show(Source $source)
    {
        throw new NotFoundHttpException;
    }

    /**
     * Show the form for editing the specified resource.
     * Not used - modal is used instead.
     */
    public function edit(Source $source)
    {
        throw new NotFoundHttpException;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Source $source)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'collector_key' => ['required', 'string', 'max:100', 'unique:sources,collector_key,'.$source->id],
            'active' => ['boolean'],
        ]);

        $source->update($validated);

        return redirect()->route('admin.sources.index')->with('status', 'Source mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Source $source)
    {
        $source->delete();

        return redirect()->route('admin.sources.index')->with('status', 'Source supprimée avec succès.');
    }

    /**
     * Toggle the active status of the source.
     */
    public function toggle(Source $source)
    {
        $source->update(['active' => ! $source->active]);

        return redirect()->back()->with('status', 'Statut de la source mis à jour.');
    }
}
