<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MeController extends Controller
{
    public function favorites(Request $request)
    {
        return response()->json(
            $request->user()->favorites()->with(['genres', 'moods'])->paginate(12)
        );
    }

    public function addFavorite(Request $request, int $titleId)
    {
        Title::findOrFail($titleId);

        $user = $request->user();

        if (!$user->favorites()->where('titles.id', $titleId)->exists()) {
            $user->favorites()->attach($titleId);
        }

        return response()->json(['ok' => true]);
    }

    public function removeFavorite(Request $request, int $titleId)
    {
        $request->user()->favorites()->detach($titleId);
        return response()->json(['ok' => true]);
    }

    public function watchlist(Request $request)
    {
        return response()->json(
            $request->user()->watchlist()->with(['genres', 'moods'])->paginate(12)
        );
    }

    public function addWatchlist(Request $request, int $titleId)
    {
        Title::findOrFail($titleId);

        $user = $request->user();

        if (!$user->watchlist()->where('titles.id', $titleId)->exists()) {
            $user->watchlist()->attach($titleId);
        }

        return response()->json(['ok' => true]);
    }

    public function removeWatchlist(Request $request, int $titleId)
    {
        $request->user()->watchlist()->detach($titleId);
        return response()->json(['ok' => true]);
    }

    public function watched(Request $request)
    {
        return response()->json(
            $request->user()->watched()->with(['genres', 'moods'])->paginate(12)
        );
    }

    public function addWatched(Request $request, int $titleId)
    {
        Title::findOrFail($titleId);

        $user = $request->user();

        if (!$user->watched()->where('titles.id', $titleId)->exists()) {
            $user->watched()->attach($titleId, ['watched_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function removeWatched(Request $request, int $titleId)
    {
        $request->user()->watched()->detach($titleId);
        return response()->json(['ok' => true]);
    }

    public function settings(Request $request)
    {
        $user = $request->user();

        $defaults = [
            'hide_email' => false,
            'hide_role' => false,
            'toast_off' => false,
            'compact_cards' => false,
        ];

        $settings = array_merge($defaults, $user->settings ?? []);

        return response()->json(['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'hide_email' => ['sometimes', 'boolean'],
            'hide_role' => ['sometimes', 'boolean'],
            'toast_off' => ['sometimes', 'boolean'],
            'compact_cards' => ['sometimes', 'boolean'],
        ]);

        $current = $user->settings ?? [];
        $user->settings = array_merge($current, $data);
        $user->save();

        return response()->json(['settings' => $user->settings]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'min:2', 'max:80'],
            'email' => [
                'sometimes',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        if (array_key_exists('name', $data)) $user->name = $data['name'];
        if (array_key_exists('email', $data)) $user->email = $data['email'];

        $user->save();

        return response()->json(['user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Mevcut şifre yanlış.'], 422);
        }

        $user->password = $data['password'];
        $user->save();

        return response()->json(['ok' => true]);
    }
}
