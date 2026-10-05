<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function store(ExperienceRequest $request): RedirectResponse
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);

        $profile->experiences()->create($request->validated());

        return redirect()->route('candidate.profile.edit')->with('status', 'experience-added');
    }

    public function update(ExperienceRequest $request, Experience $experience): RedirectResponse
    {
        abort_unless($experience->candidateProfile->user_id === $request->user()->id, 403);

        $experience->update($request->validated());

        return redirect()->route('candidate.profile.edit')->with('status', 'experience-updated');
    }

    public function destroy(Request $request, Experience $experience): RedirectResponse
    {
        abort_unless($experience->candidateProfile->user_id === $request->user()->id, 403);

        $experience->delete();

        return redirect()->route('candidate.profile.edit')->with('status', 'experience-deleted');
    }
}
