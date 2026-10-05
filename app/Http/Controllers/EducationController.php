<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducationRequest;
use App\Models\Education;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function store(EducationRequest $request): RedirectResponse
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);

        $profile->educations()->create($request->validated());

        return redirect()->route('candidate.profile.edit')->with('status', 'education-added');
    }

    public function update(EducationRequest $request, Education $education): RedirectResponse
    {
        abort_unless($education->candidateProfile->user_id === $request->user()->id, 403);

        $education->update($request->validated());

        return redirect()->route('candidate.profile.edit')->with('status', 'education-updated');
    }

    public function destroy(Request $request, Education $education): RedirectResponse
    {
        abort_unless($education->candidateProfile->user_id === $request->user()->id, 403);

        $education->delete();

        return redirect()->route('candidate.profile.edit')->with('status', 'education-deleted');
    }
}
