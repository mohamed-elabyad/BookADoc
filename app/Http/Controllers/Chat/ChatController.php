<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\StoreConversationRequest;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Doctor;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $chatService
    ) {}

    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['doctor.user', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        Gate::authorize('view', $conversation);

        $this->chatService->markMessagesAsRead($conversation, $user);

        $conversation->load('doctor.user');

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', compact('conversation', 'messages'));
    }


    public function store(StoreConversationRequest $request)
    {
        $request->validated();

        $appointment = Appointment::findOrFail($request->appointment_id);

        if (!in_array($appointment->status->value, ['confirmed', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'You can only chat for confirmed or completed appointments'
            ], 403);
        }

        $user = Auth::user();

        if ($user->role->value === 'user' && $appointment->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->role->value === 'doctor' && $appointment->doctor_id !== $user->doctor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'doctor_id' => $appointment->doctor_id,
                'user_id' => $appointment->user_id,
            ],
            [
                'appointment_id' => $appointment->id,
                'last_message_at' => now(),
            ]
        );

        $message = null;
        if ($request->message) {
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'message' => $request->message,
            ]);

            $conversation->update(['last_message_at' => now()]);

            $message->load('sender.doctor');
            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => $message ? $message->load('sender') : null,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation)
    {
        $request->validated();
        
        Gate::authorize('sendMessage', $conversation);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'last_message_at' => now()
        ]);


        $message->load('sender.doctor');

        $sender_image = null;
        if ($message->sender->doctor?->image) {
            $image = $message->sender->doctor->image;
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $sender_image = $image;
            } elseif (str_starts_with($image, 'doctors/')) {
                $sender_image = asset('storage/' . $image);
            } else {
                $sender_image = asset($image);
            }
        }

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_image' => $sender_image,
                'message' => $message->message,
                'is_read' => false,
                'created_at' => $message->created_at->toDateTimeString(),
            ]
        ]);
    }

    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();

        Gate::authorize('view', $conversation);

        $this->chatService->markMessagesAsRead($conversation, $user);

        return response()->json(['success' => true]);
    }
}
