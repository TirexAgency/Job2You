<?php

namespace App\Http\Controllers;

use App\Models\CandidateSkill;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CandidateSkillController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skill_id' => ['nullable', 'exists:skills,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'level' => ['required', 'integer', 'between:1,5'],
        ]);

        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);

        if (! empty($validated['skill_id'])) {
            $skill = Skill::findOrFail($validated['skill_id']);
        } else {
            $request->validate(['name' => ['required', 'string', 'max:100']]);
            $skill = Skill::firstOrCreate(
                ['normalized_name' => mb_strtolower(trim($validated['name']))],
                ['name' => trim($validated['name'])]
            );
        }

        $profile->candidateSkills()->updateOrCreate(
            ['skill_id' => $skill->id],
            ['level' => $validated['level'], 'weight' => 1.00]
        );

        return redirect()->route('candidate.profile.edit')->with('status', 'skill-added');
    }

    public function destroy(Request $request, CandidateSkill $candidateSkill): RedirectResponse
    {
        abort_unless($candidateSkill->candidateProfile->user_id === $request->user()->id, 403);

        CandidateSkill::where('candidate_profile_id', $candidateSkill->candidate_profile_id)
            ->where('skill_id', $candidateSkill->skill_id)
            ->delete();

        return redirect()->route('candidate.profile.edit')->with('status', 'skill-deleted');
    }
}
