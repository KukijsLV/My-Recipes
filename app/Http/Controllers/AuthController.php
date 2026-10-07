<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);
        $validated['email'] = strtolower(trim($validated['email']));

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
        $isOwnProfile = Auth::id() === $user->id;
        $recipes = $user->recipes()
            ->with('user')
            ->when(! $isOwnProfile, fn ($query) => $query->where('visibility', 'public'))
            ->latest()
            ->paginate(12);

        return view('auth.profile', [
            'user' => $user,
            'recipes' => $recipes,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'confirmed', 'string', 'min:8'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $validated['email'] = strtolower(trim($validated['email']));

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Parole nav pareizi.'])->withInput();
        }

        $oldProfileImage = $user->profile_image;
        $newProfileImage = $oldProfileImage;
        if ($request->hasFile('profile_image')) {
            $newProfileImage = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'profile_image' => $newProfileImage,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($oldProfileImage && $oldProfileImage !== $newProfileImage) {
            Storage::disk('public')->delete($oldProfileImage);
        }

        return to_route('profile')->with('status', 'Profils atjaunināts.');
    }

    public function toggleUserBlock(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin?->is_admin || $admin->id === $user->id) {
            abort(403);
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->whereKeyNot($user->id)->count() === 0) {
            abort(403, 'Nav pieejami aktīvi administratori.');
        }

        $user->update([
            'is_blocked' => ! $user->is_blocked,
        ]);

        $status = $user->is_blocked ? 'Lietotājs ir bloķēts.' : 'Lietotāja bloķēšana ir noņemta.';

        return back()->with('status', $status);
    }

    public function showForgotPassword(): View
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        $validated['email'] = strtolower(trim($validated['email']));

        $status = Password::sendResetLink($validated);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Paroles atjaunašanas norāde ir nosūtīta.')
            : back()->withErrors(['email' => trans($status)]);
    }

    public function showResetPassword(string $token): View
    {
        return view('auth.passwords.reset', compact('token'));
    }

    public function resetPassword(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ]);

        $status = Password::reset([
            ...$validated,
            'token' => $token,
        ]);

        if ($status !== Password::RESET_COMPLETED) {
            return back()->withErrors(['token' => trans($status)]);
        }

        return to_route('login')->with('status', 'Parole ir mainīta.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
