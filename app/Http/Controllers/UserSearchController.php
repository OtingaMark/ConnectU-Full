<?php

namespace App\Http\Controllers;

use App\Models\PeerConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSearchController extends Controller
{
    /**
     * Handle index.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = collect();

        if ($search) {
            $users = User::with('profile')
                ->active()
                ->where('id', '!=', Auth::id())
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->get();

            $users = $users->map(function ($user) {
                $connection = PeerConnection::where(function ($query) use ($user) {
                    $query->where('requester_id', Auth::id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('requester_id', $user->id)
                        ->where('receiver_id', Auth::id());
                })->first();

                $user->connection_status = $connection?->status;

                return $user;
            });
        }

        return view('users.search', compact('users', 'search'));
    }
}
