<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateProfileRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateProfileController extends Controller
{
    /**
     * Affiche le formulaire complet du profil candidat.
     */
    public function edit(Request $request): View
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['experience_level' => 'junior']
        );

        $skillSearch = $request->string('q')->trim();
        $skills = Skill::query()
            ->when($skillSearch !== '', fn ($query) => $query->where('name', 'like', '%'.$skillSearch.'%'))
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('candidate.profile', [
            'profile' => $profile->load(['experiences', 'educations', 'candidateSkills.skill']),
            'skills' => $skills,
            'skillSearch' => $skillSearch,
        ]);
    }

    /**
     * Sauvegarde le profil, la localisation/mobilité et les préférences.
     */
    public function update(CandidateProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['experience_level' => 'junior']
        );

        $validated = $request->validated();
        unset($validated['sectors_text']);

        $profile->fill($validated);
        $profile->location = $request->validated('city');
        $profile->sectors = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('sectors_text')))
            ->map(fn ($sector) => trim($sector))
            ->filter()
            ->values()
            ->all();
        $profile->alerts_enabled = $request->boolean('alerts_enabled');
        $profile->save();

        return redirect()->route('candidate.profile.edit')->with('status', 'profile-updated');
    }
}
