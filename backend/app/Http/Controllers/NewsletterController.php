<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ConfirmSubscriptionMail;

class NewsletterController extends Controller
{
    // Newsletter 
    // POST /api/newsletter/subscribe
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'email' => $request->email,
            'unsubscribe_token' => Str::random(60),
            'verified_at' => null,
        ]);

        Mail::to($subscriber->email)->send(new ConfirmSubscriptionMail($subscriber));

        return response()->json([
            'message' => 'Please check your email to confirm subscription.',
            'subscriber' => $subscriber->only(['email', 'created_at']),
        ]);
    }

    // GET /api/newsletter/confirm/{id}
    public function confirm($id)
    {
        $subscriber = Subscriber::findOrFail($id);

        if ($subscriber->verified_at) {
            return response()->json(['message' => 'Already confirmed']);
        }

        $subscriber->update([
            'verified_at' => now(),
        ]);

        return response()->json(['message' => 'Subscription confirmed.']);
    }

    // GET /api/newsletter/unsubscribe/{token}
    public function unsubscribe($token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        $subscriber->delete();

        return response()->json(['message' => 'You have been unsubscribed.']);
    }

    // GET /api/admin/subscribers
    public function subscribersList(Request $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subscribers = Subscriber::verified()
            ->latest()
            ->paginate(20);

        return response()->json($subscribers);
    }
}
