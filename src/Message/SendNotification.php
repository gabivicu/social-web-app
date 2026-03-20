<?php

namespace App\Message;

class SendNotification
{
    public function __construct(
        private int $ownerId,
        private int $actorId,
        private string $type,
    ) {
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getActorId(): int
    {
        return $this->actorId;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
