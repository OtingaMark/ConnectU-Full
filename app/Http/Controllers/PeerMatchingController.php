<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\PeerConnection;
use Illuminate\Support\Facades\Auth;

class PeerMatchingController extends Controller
{
    public function index()
    {
        $myProfile = Auth::user()->profile;

        if (!$myProfile) {
            return redirect()->route('profile.edit')
                ->with('success', 'Please complete your profile before finding peers.');
        }

        $profiles = Profile::with('user.profile')
            ->whereHas('user', function ($query) {
                $query->active();
            })
            ->where('user_id', '!=', Auth::id())
            ->get();

        $matches = $profiles->map(function ($profile) use ($myProfile) {

            $score = 0;

            if (
                $myProfile->course &&
                $profile->course &&
                strtolower($myProfile->course) === strtolower($profile->course)
            ) {
                $score += 30;
            }

            $myInterests = $this->splitWords($myProfile->interests);
            $peerInterests = $this->splitWords($profile->interests);

            $sharedInterests = array_intersect(
                $myInterests,
                $peerInterests
            );

            $score += count($sharedInterests) * 10;

            $mySkills = $this->splitWords($myProfile->skills);
            $peerSkills = $this->splitWords($profile->skills);

            $sharedSkills = array_intersect(
                $mySkills,
                $peerSkills
            );

            $score += count($sharedSkills) * 10;

            $courseScoreMax = (
                $myProfile->course &&
                $profile->course
            ) ? 30 : 0;

            $availabilityScoreMax = (
                $myProfile->availability &&
                $profile->availability
            ) ? 20 : 0;

            $interestScoreMax = min(count($myInterests), count($peerInterests)) * 10;
            $skillScoreMax = min(count($mySkills), count($peerSkills)) * 10;

            $maxPossibleScore = $courseScoreMax + $availabilityScoreMax + $interestScoreMax + $skillScoreMax;

            if (
                $myProfile->availability &&
                $profile->availability &&
                strtolower($myProfile->availability) === strtolower($profile->availability)
            ) {
                $score += 20;
            }

            $connection = PeerConnection::where(function ($query) use ($profile) {

                $query->where('requester_id', Auth::id())
                    ->where('receiver_id', $profile->user_id);

            })->orWhere(function ($query) use ($profile) {

                $query->where('requester_id', $profile->user_id)
                    ->where('receiver_id', Auth::id());

            })->first();

            $profile->raw_match_score = $score;

            $profile->match_score = $maxPossibleScore > 0
                ? (int) round(min(100, ($score / $maxPossibleScore) * 100))
                : 0;

            $profile->shared_interests =
                implode(', ', $sharedInterests);

            $profile->shared_skills =
                implode(', ', $sharedSkills);

            $profile->connection = $connection;

            return $profile;

        })
        ->filter(function ($profile) {
            return $profile->raw_match_score > 0;
        })
        ->sortByDesc('match_score');

        return view(
            'peer-matching.index',
            compact('matches')
        );
    }

    private function splitWords($value)
    {
        if (!$value) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(function ($item) {
                return strtolower(trim($item));
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}