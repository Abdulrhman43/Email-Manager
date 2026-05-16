<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chat}', function ($user, Chat $chat) {
    return (int) $user->id === (int) $chat->user1_id || (int) $user->id === (int) $chat->user2_id;
});