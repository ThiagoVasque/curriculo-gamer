<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showPublicProfile($id)
    {
        $myId = Auth::id();

        // Carrega o usuário do perfil com os jogos e amigos dele
        $user = \App\Models\User::with('games')->findOrFail($id);

        // Busca os amigos DESSE perfil (seja o seu ou do seu amigo)
        $amigos = \App\Models\Friendship::where(function ($q) use ($id) {
            $q->where('user_id', $id)->orWhere('friend_id', $id);
        })
            ->where('status', 'accepted')
            ->with(['user', 'friend'])
            ->get()
            ->map(function ($f) use ($id) {
                return $f->user_id == $id ? $f->friend : $f->user;
            })->filter();

        // Se eu estiver visitando o perfil de OUTRA pessoa, preciso saber o status da amizade entre NÓS
        $friendship = null;
        if ($id != $myId) {
            $friendship = \App\Models\Friendship::where(function ($q) use ($myId, $id) {
                $q->where('user_id', $myId)->where('friend_id', $id);
            })->orWhere(function ($q) use ($myId, $id) {
                $q->where('user_id', $id)->where('friend_id', $myId);
            })->first();
        }

        $stats = [
            'platinas' => $user->games->where('status', 'platinado')->count(),
            'amigos_count' => $amigos->count()
        ];

        return view('profile.public', compact('user', 'friendship', 'stats', 'amigos'));
    }
}
