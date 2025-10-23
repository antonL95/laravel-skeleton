<?php

declare(strict_types=1);

namespace App\Events;

use App\Data\FlashData;
use App\Enums\FlashMessageType;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class FlashMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly FlashData $flashData,
    ) {}

    /**
     * @return PrivateChannel[]
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->user->id),
        ];
    }

    /**
     * @return array<string, FlashMessageType|string|null>
     */
    public function broadcastWith(): array
    {
        return $this->flashData->toArray(); // @phpstan-ignore-line
    }
}
