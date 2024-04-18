<?php

namespace App\Jobs;

use App\Events\MessageBroadcast;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Swap;
use App\Services\SwapNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SwapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Swap $swap;
    public Conversation $conversation;
    public Message $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Swap $swap, Conversation $conversation, Message $message)
    {
        $this->swap = $swap;
        $this->conversation = $conversation;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SwapNotificationService::sendNotification(
            $this->swap,
            [$this->swap->exchanged_user_id],
            'You have a new swap request ' . $this->swap->uid
        );

        event(new MessageBroadcast($this->conversation, $this->message));

    }
}
