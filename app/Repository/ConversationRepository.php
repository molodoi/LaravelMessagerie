<?php
namespace App\Repository;

use App\User;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ConversationRepository
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var Message
     */
    private $message;

    public function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function getConversations($userId)
    {
        $conversations = $this->user->newQuery()
            ->select('name', 'id')
            ->where('id', '!=', $userId)
            ->get();
        $unread = $this->unreadCount($userId);
        foreach ($conversations as $conversation) {
            if (isset($unread[$conversation->id])) {
                $conversation->unread = $unread[$conversation->id];
                continue;
            }
            $conversation->unread = 0;
        }
        return $conversations;
    }

    public function createMessage($content, $from, $to)
    {
        return $this->message->newQuery()->create(
            [
                'content' => $content,
                'from_id' => $from,
                'to_id' => $to,
                'created_at' => Carbon::now()
            ]
        );
    }

    /**
     * Récupère les messages
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function getMessagesFor(int $from, int $to) : Builder
    {
        return $this->message->newQuery()
            ->whereRaw("((from_id = $from AND to_id = $to) OR (from_id = $to AND to_id = $from))")
            ->orderBy('created_at', 'DESC')
            ->with([
                'from' => function ($query) {
                    return $query->select('name', 'id');
                }
            ]);
    }


    /**
     * Récupère le nombre de messages non lu pour chaque converstions
     * @param int $userId
     * @return array
     */
    public function unreadCount(int $userId)
    {
        return $this->message->newQuery()
            ->where('to_id', $userId)
            ->groupBy('from_id')
            ->selectRaw('from_id, COUNT(id) as count')
            ->whereRaw('read_at IS NULL')
            ->get()
            ->pluck('count', 'from_id');
    }


    /**
     * Marque tous les messages de cet utilisateur comme lu
     * @param int $from
     * @param int $to
     */
    public function readAllFrom(int $from, int $to)
    {
        $this->message->where('from_id', $from)->where('to_id', $to)->update(['read_at' => Carbon::now()]);
    }

}