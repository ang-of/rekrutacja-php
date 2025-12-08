<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function user() {
        return request()->user();
    }

    public function users() {
        return response()->json(User::all()->map(function($q) {
            return collect($q)->only(['id', 'name']);
        }), 200);
    }

    public function follow(Request $request, $id)
    {
        $user = $request->user();
        
        // Sprawdź czy użytkownik istnieje
        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Sprawdź czy nie próbuje obserwować sam siebie
        if ($user->id == $id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }
        
        // Sprawdź czy już nie obserwuje
        if ($user->follows()->where('friend_id', $id)->exists()) {
            return response()->json(['message' => 'Already following this user'], 200);
        }
        
        // Dodaj obserwację
        $user->follows()->attach($id, ['status' => 0]);
        
        return response()->json(['message' => 'User followed successfully'], 200);
    }

    public function unfollow(Request $request, $id)
    {        
        
        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Sprawdź czy obserwuje tego użytkownika
        if (!user()->follows()->where('friend_id', $id)->exists()) {
            return response()->json(['error' => 'Not following this user'], 400);
        }
        
        // Usuń obserwację
        user()->follows()->detach($id);
        
        return response()->json(['message' => 'User unfollowed successfully'], 200);
    }

    public function invites() {
        
        $invites = user()->follows()
            ->wherePivot('status', 0)
            ->get(['users.id', 'users.name']);
        return response()->json($invites, 200);
    }

    public function accept($id) {
        // Sprawdź czy zaproszenie istnieje i nie jest już zaakceptowane
        $invite = DB::table('user_user')
            ->where('user_id', $id)
            ->where('friend_id', user()->id)
            ->where('status', 0)
            ->first();

        if (!$invite) {
            return response()->json(['error' => 'Invitation does not exist or has already been accepted'], 400);
        }

        DB::table('user_user')->updateOrInsert([
            'user_id' => $id,
            'friend_id' => user()->id
        ], [
            'status' => 1
        ]);

        return $this->friends();
    }

    public function decline($id) {
        DB::table('user_user')
            ->where('user_id', user()->id)
            ->where('friend_id', $id)->delete();

        return $this->friends();
    }

    public function friends() {
        $friends = user()->follows()
            ->wherePivot('status', 1)
            ->get(['users.id', 'users.name', 'users.email']);
        
        return response()->json($friends, 200);
    }
}
