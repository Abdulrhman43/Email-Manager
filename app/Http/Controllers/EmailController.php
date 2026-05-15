<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Email;
use App\Models\User;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\StoreReplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    // ── INDEX — main inbox page ───────────────────────────────────────────────
    public function index()
    {
        $user    = Auth::user();
        $user_id = $user->id;

        // Get all chats this user is part of, with latest email and both users
        $chats = Chat::with(['user1', 'user2', 'emails.sender'])
            ->where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->get();

        // Build the same $emails array structure the Blade view expects
        $emails = [];

        foreach ($chats as $chat) {
            $allEmails  = $chat->emails;
            if ($allEmails->isEmpty()) continue;

            $latestEmail = $allEmails->last();
            $isSent      = $latestEmail->sender_id === $user_id;
            $other       = $chat->otherUser($user_id);

            $thread = $allEmails->map(function ($msg) use ($user_id) {
                return [
                    'from'       => $msg->sender_id === $user_id ? 'You' : $msg->sender->name,
                    'body'       => $msg->message,
                    'time'       => $msg->created_at->format('M d, h:i A'),
                    'attachment' => $msg->attachment,
                ];
            })->values()->toArray();

            $emails[] = [
                'messageId'    => $latestEmail->id,
                'threadId'     => $chat->id,
                'type'         => $isSent ? 'sent' : 'received',
                'from'         => $isSent ? $user->name  : $other->name,
                'fromEmail'    => $isSent ? $user->email : $other->email,
                'fromInitials' => $this->initials($isSent ? $user->name : $other->name),
                'to'           => $isSent ? $other->name  : $user->name,
                'toEmail'      => $isSent ? $other->email : $user->email,
                'toInitials'   => $this->initials($isSent ? $other->name : $user->name),
                'subject'      => $latestEmail->subject,
                'body'         => $latestEmail->message,
                'time'         => $latestEmail->created_at->format('M d, h:i A'),
                'status'       => 'New',
                'thread'       => $thread,
            ];
        }

        // Sort by most recent first
        usort($emails, fn($a, $b) => strcmp($b['time'], $a['time']));

        return view('inbox', compact('emails', 'user'));
    }

    // ── READ — return all emails as JSON (used by AJAX) ───────────────────────
    public function read()
    {
        $user_id = Auth::id();

        $chats = Chat::with(['user1', 'user2', 'emails.sender'])
            ->where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->get();

        $data = [];

        foreach ($chats as $chat) {
            foreach ($chat->emails as $email) {
                $other = $chat->otherUser($user_id);
                $data[] = [
                    'id'             => $email->id,
                    'chat_id'        => $chat->id,
                    'subject'        => $email->subject,
                    'message'        => $email->message,
                    'attachment'     => $email->attachment,
                    'sent_at'        => $email->created_at->format('Y-m-d H:i:s'),
                    'sender_name'    => $email->sender->name,
                    'sender_email'   => $email->sender->email,
                    'recipient_name' => $other->name,
                    'recipient_email'=> $other->email,
                ];
            }
        }

        // Sort newest first
        usort($data, fn($a, $b) => strcmp($b['sent_at'], $a['sent_at']));

        return response()->json($data);
    }

    // ── STORE — compose and send a new email ─────────────────────────────────
    public function store(StoreEmailRequest $request)
    {
        $user       = Auth::user();
        $receiver   = User::where('email', $request->composeEmail)->first();
        $attachment = null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file       = $request->file('attachment');
            $filename   = bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $attachment = $filename;
        }

        // Create chat
        $chat = Chat::create([
            'user1_id' => $user->id,
            'user2_id' => $receiver->id,
        ]);

        // Create email
        Email::create([
            'chat_id'    => $chat->id,
            'sender_id'  => $user->id,
            'subject'    => $request->composeSubject,
            'message'    => $request->composeBody,
            'attachment' => $attachment,
        ]);

        return response()->json([
            'message'  => 'Email added',
            'chat_id'  => $chat->id,
        ]);
    }

    // ── REPLY — add a message to an existing chat ─────────────────────────────
    public function reply(StoreReplyRequest $request, Chat $chat)
    {
        $user = Auth::user();

        // Make sure the user is part of this chat
        if ($chat->user1_id !== $user->id && $chat->user2_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $attachment = null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file       = $request->file('attachment');
            $filename   = bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $attachment = $filename;
        }

        // Get subject from first email in chat
        $subject = $chat->emails()->first()->subject ?? '';

        Email::create([
            'chat_id'    => $chat->id,
            'sender_id'  => $user->id,
            'subject'    => $subject,
            'message'    => $request->message,
            'attachment' => $attachment,
        ]);

        return response()->json(['message' => 'Reply added', 'attachment' => $attachment]);
    }

    // ── DESTROY — delete a chat and all its emails ────────────────────────────
    public function destroy(Chat $chat)
    {
        $user = Auth::user();

        // Make sure the user is part of this chat
        if ($chat->user1_id !== $user->id && $chat->user2_id !== $user->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $count = $chat->emails()->count();
        $chat->delete(); // cascades to emails via FK

        return response()->json(['message' => "Deleted $count messages and associated chats"]);
    }

    // ── Helper: generate initials from a name ────────────────────────────────
    private function initials(string $name): string
    {
        return strtoupper(
            implode('', array_map(
                fn($w) => mb_substr($w, 0, 1),
                array_filter(explode(' ', trim($name)))
            ))
        );
    }
}