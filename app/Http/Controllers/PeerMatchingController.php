<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\PeerConnection;
use Illuminate\Support\Facades\Auth;

class PeerMatchingController extends Controller
{
    /**
     * Display the main page data for this feature.
     */
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

            if ($availabilityScoreMax > 0) {
                $availabilityRatio = $this->availabilityMatchRatio(
                    $myProfile->availability,
                    $profile->availability
                );

                $score += (int) round($availabilityRatio * $availabilityScoreMax);
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

    /**
     * Split free text into normalized searchable terms.
     */
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

    /**
     * Compute partial availability compatibility in range 0..1.
     */
    private function availabilityMatchRatio(?string $mine, ?string $peer): float
    {
        $mine = strtolower(trim((string) $mine));
        $peer = strtolower(trim((string) $peer));

        if ($mine === '' || $peer === '') {
            return 0.0;
        }

        if ($mine === $peer) {
            return 1.0;
        }

        $myDays = $this->extractAvailabilityDays($mine);
        $peerDays = $this->extractAvailabilityDays($peer);
        $myTimes = $this->extractAvailabilityTimes($mine);
        $peerTimes = $this->extractAvailabilityTimes($peer);

        $dayRatio = $this->setOverlapRatio($myDays, $peerDays);
        $timeRatio = $this->setOverlapRatio($myTimes, $peerTimes);

        // If times are not explicitly recognized, fall back to day-only overlap.
        if (empty($myTimes) || empty($peerTimes)) {
            return $dayRatio;
        }

        // Days are more important, but matching time blocks still matters.
        return ($dayRatio * 0.6) + ($timeRatio * 0.4);
    }

    /**
     * Parse day tokens and common day groups from free text availability.
     */
    private function extractAvailabilityDays(string $value): array
    {
        $days = [];

        $map = [
            'monday' => ['monday'],
            'mon' => ['monday'],
            'tuesday' => ['tuesday'],
            'tue' => ['tuesday'],
            'tues' => ['tuesday'],
            'wednesday' => ['wednesday'],
            'wed' => ['wednesday'],
            'thursday' => ['thursday'],
            'thu' => ['thursday'],
            'thur' => ['thursday'],
            'thurs' => ['thursday'],
            'friday' => ['friday'],
            'fri' => ['friday'],
            'saturday' => ['saturday'],
            'sat' => ['saturday'],
            'sunday' => ['sunday'],
            'sun' => ['sunday'],
            'everyday' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'every day' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'daily' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'all days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'weekdays' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'weekday' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'weekends' => ['saturday', 'sunday'],
            'weekend' => ['saturday', 'sunday'],
        ];

        foreach ($map as $token => $expandedDays) {
            if ($this->containsToken($value, $token)) {
                $days = array_merge($days, $expandedDays);
            }
        }

        return array_values(array_unique($days));
    }

    /**
     * Parse common time-slot labels from free text availability.
     */
    private function extractAvailabilityTimes(string $value): array
    {
        $times = [];

        $map = [
            'morning' => ['morning'],
            'am' => ['morning'],
            'afternoon' => ['afternoon'],
            'noon' => ['afternoon'],
            'evening' => ['evening'],
            'pm' => ['evening'],
            'night' => ['night'],
            'late' => ['night'],
            'late night' => ['night'],
        ];

        foreach ($map as $token => $expandedTimes) {
            if ($this->containsToken($value, $token)) {
                $times = array_merge($times, $expandedTimes);
            }
        }

        return array_values(array_unique($times));
    }

    /**
     * Return overlap ratio against the smaller set to reward subset matches.
     */
    private function setOverlapRatio(array $left, array $right): float
    {
        if (empty($left) || empty($right)) {
            return 0.0;
        }

        $intersectionCount = count(array_intersect($left, $right));
        $baseline = min(count($left), count($right));

        return $baseline > 0 ? min(1.0, $intersectionCount / $baseline) : 0.0;
    }

    /**
     * Match a token with word boundaries to avoid accidental substring matches.
     */
    private function containsToken(string $value, string $token): bool
    {
        return (bool) preg_match(
            '/(^|[^a-z])' . preg_quote($token, '/') . '([^a-z]|$)/',
            $value
        );
    }
}