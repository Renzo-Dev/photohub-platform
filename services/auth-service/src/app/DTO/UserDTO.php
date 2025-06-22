<?php

namespace App\DTO;

use App\Models\User;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $createdAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            createdAt: optional($user->created_at)?->toDateTimeString(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
