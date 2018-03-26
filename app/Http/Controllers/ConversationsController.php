<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Notifications\MessageReceived;
use App\Repository\ConversationRepository;
use Illuminate\Auth\AuthManager;
use App\User;

class ConversationsController extends Controller
{

    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * @var AuthManager
     */
    private $authManager;

    public function __construct(ConversationRepository $conversationRepository, AuthManager $authManager)
    {
        $this->middleware('auth');
        $this->conversationRepository = $conversationRepository;
        $this->authManager = $authManager;
    }

    public function index(){
        return view('conversations/index');
    }
    
    public function show(User $user){

        $me = $this->authManager->user();
        $messages = $this->conversationRepository->getMessagesFor($me->id, $user->id)->get()->reverse();
        $unread = $this->conversationRepository->unreadCount($me->id);

        if (isset($unread[$user->id])) {
            $this->conversationRepository->readAllFrom($user->id, $me->id);
            $unread[$user->id] = 0;
        }

        return view('conversations/show', [
            'user' => $user,
            'me' => $me,
            'users' => $this->conversationRepository->getConversations($this->authManager->user()->id),
            'messages' => $messages,
            'unread' => $this->conversationRepository->unreadCount($this->authManager->user()->id),
        ]);
    }

    public function store(User $user, StoreMessageRequest $request)
    {
        $message = $this->conversationRepository->createMessage(
            $request->get('content'),
            $this->authManager->user()->id,
            $user->id
        );

        $user->notify(new MessageReceived($message));

        return redirect(route('conversations.show', ['id' => $user->id]));
    }


}
