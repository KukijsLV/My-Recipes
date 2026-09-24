<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Šie piekļuves dati nav pareizi.'])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user && $user->is_blocked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Tavs konts ir bloķēts.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return to_route('recipes.index');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $user = User::create($validated);
        Auth::login($user);
        $request->session()->regenerate();

        return to_route('recipes.index');
    }

    public function showProfile(): View
    {
        $user = Auth::user();

        return $this->showUserProfile($user);
    }

    public function showUserProfile(User $user): View
    {
        $recipes = $user->recipes()->with('user')->latest()->get();

        return view('auth.profile', [
            'user' => $user,
            'recipes' => $recipes,
            'isOwnProfile' => Auth::id() === $user->id,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', 'string', 'min:8'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $validated['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'profile_image' => $validated['profile_image'] ?? $user->profile_image,
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return to_route('profile')->with('status', 'Profils atjaunināts.');
    }

    public function toggleUserBlock(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()?->is_admin) {
            abort(403);
        }

        $user->update([
            'is_blocked' => ! $user->is_blocked,
        ]);

        $status = $user->is_blocked ? 'Lietotājs ir bloķēts.' : 'Lietotāja bloķēšana ir noņemta.';

        return back()->with('status', $status);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
