<?php

namespace Tests\Feature;

use App\Mail\ContactAdminMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_is_available_to_guests_and_shows_the_navigation_link(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Kontakti')
            ->assertSee('name="message"', false);
    }

    public function test_contact_message_is_sent_to_admin_with_a_reply_address(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->post(route('contact.send'), [
            'name' => 'Anna Bērziņa',
            'email' => 'anna@example.com',
            'subject' => 'Jautājums par recepti',
            'message' => 'Kā pievienot attēlu?',
        ])->assertRedirect(route('contact'))
            ->assertSessionHas('status');

        Mail::assertQueued(ContactAdminMail::class, function (ContactAdminMail $mail) use ($admin): bool {
            $replyTo = $mail->envelope()->replyTo[0] ?? null;

            return $mail->hasTo($admin->email)
                && $replyTo?->address === 'anna@example.com';
        });
    }

    public function test_invalid_contact_message_is_not_sent(): void
    {
        Mail::fake();
        User::factory()->create(['is_admin' => true]);

        $this->post(route('contact.send'), [
            'name' => 'Anna',
            'email' => 'not-an-email',
            'subject' => 'Jautājums',
            'message' => '',
        ])->assertSessionHasErrors(['email', 'message']);

        Mail::assertNothingOutgoing();
    }

    public function test_contact_message_is_not_sent_when_there_is_no_admin(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), [
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'subject' => 'Jautājums',
            'message' => 'Sveiki!',
        ])->assertSessionHasErrors('contact');

        Mail::assertNothingOutgoing();
    }
}
