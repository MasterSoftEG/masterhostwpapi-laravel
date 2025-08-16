<?php

namespace masterhosteg\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header(config('masterhosteg.webhook_signature_header'));
        $secret = config('masterhosteg.webhook_secret');

        if (!$signature || !$secret || $signature !== $secret) {
            return response('Invalid signature', 400);
        }

        $payload = $request->json()->all();
        if (!isset($payload['event'])) {
            return response('Invalid payload', 400);
        }

        $eventMap = [
            'chats.upsert' => \masterhosteg\Events\ChatsUpserted::class,
            'chats.update' => \masterhosteg\Events\ChatsUpdated::class,
            'chats.delete' => \masterhosteg\Events\ChatsDeleted::class,
            'groups.upsert' => \masterhosteg\Events\GroupsUpserted::class,
            'groups.update' => \masterhosteg\Events\GroupsUpdated::class,
            'group-participants.update' => \masterhosteg\Events\GroupParticipantsUpdated::class,
            'contacts.upsert' => \masterhosteg\Events\ContactsUpserted::class,
            'contacts.update' => \masterhosteg\Events\ContactsUpdated::class,
            'messages.upsert' => \masterhosteg\Events\MessagesUpserted::class,
            'messages.update' => \masterhosteg\Events\MessagesUpdated::class,
            'messages.delete' => \masterhosteg\Events\MessagesDeleted::class,
            'messages.reaction' => \masterhosteg\Events\MessagesReaction::class,
            'message-receipt.update' => \masterhosteg\Events\MessageReceiptUpdated::class,
            'message.sent' => \masterhosteg\Events\MessageSent::class,
            'session.status' => \masterhosteg\Events\SessionStatus::class,
            'qrcode.updated' => \masterhosteg\Events\QrCodeUpdated::class,
        ];
        $eventType = $payload['event'];
        $eventClass = $eventMap[$eventType] ?? \masterhosteg\Events\masterhostWebhookEvent::class;
        Event::dispatch(new $eventClass($payload));

        return response('OK', 200);
    }
} 