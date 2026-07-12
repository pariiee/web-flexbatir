<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * List all conversations for the authenticated user,
     * with latest message and unread count.
     */
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'latestMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function (Conversation $conv) use ($userId) {
                $other = $conv->otherUser($userId);
                return [
                    'id'             => $conv->id,
                    'other_user'     => [
                        'id'         => $other->id,
                        'name'       => $other->name,
                        'username'   => $other->username,
                        'avatar_url' => $other->avatar_url,
                    ],
                    'latest_message' => $conv->latestMessage ? [
                        'id'         => $conv->latestMessage->id,
                        'body'       => $conv->latestMessage->body,
                        'sender_id'  => $conv->latestMessage->sender_id,
                        'created_at' => $conv->latestMessage->created_at,
                    ] : null,
                    'unread_count'   => $conv->unreadCount($userId),
                    'updated_at'     => $conv->last_message_at ?? $conv->updated_at,
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    /**
     * Get or create a conversation with another user.
     */
    public function findOrCreate(Request $request): JsonResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $userId  = $request->user()->id;
        $otherId = (int) $request->user_id;

        if ($userId === $otherId) {
            return response()->json(['message' => 'Cannot message yourself'], 422);
        }

        $minId = min($userId, $otherId);
        $maxId = max($userId, $otherId);

        $conv = Conversation::firstOrCreate(
            ['user_one_id' => $minId, 'user_two_id' => $maxId],
        );

        $conv->load(['userOne', 'userTwo', 'latestMessage']);
        $other = $conv->otherUser($userId);

        return response()->json([
            'conversation' => [
                'id'         => $conv->id,
                'other_user' => [
                    'id'         => $other->id,
                    'name'       => $other->name,
                    'username'   => $other->username,
                    'avatar_url' => $other->avatar_url,
                ],
                'unread_count' => $conv->unreadCount($userId),
            ],
        ]);
    }

    /**
     * List messages in a conversation (paginated).
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);
        $userId = $request->user()->id;

        $messages = $conversation->messages()
            ->with('sender:id,name,username,avatar_url')
            ->orderByDesc('created_at')
            ->paginate(30);

        // Mark incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->items(),
            'has_more' => $messages->hasMorePages(),
            'next_page' => $messages->hasMorePages() ? $messages->currentPage() + 1 : null,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

        $data = $request->validate(['body' => 'required|string|max:2000']);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body'      => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        $message->load('sender:id,name,username,avatar_url');

        return response()->json(['message' => $message], 201);
    }

    private function authorizeParticipant(Request $request, Conversation $conversation): void
    {
        $userId = $request->user()->id;
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403, 'Forbidden');
        }
    }
}
