<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdminMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $admins = User::query()
            ->where('is_admin', true)
            ->where('is_blocked', false)
            ->get(['email']);

        if ($admins->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['contact' => 'Pašlaik nav pieejams administrators, kam nosūtīt ziņu.']);
        }

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new ContactAdminMail(
                contactName: $validated['name'],
                contactEmail: $validated['email'],
                contactSubject: $validated['subject'],
                contactMessage: $validated['message'],
            ));
        }

        return to_route('contact')->with('status', 'Ziņa veiksmīgi nosūtīta administratoram.');
    }
}
