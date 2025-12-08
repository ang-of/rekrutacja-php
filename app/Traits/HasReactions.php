<?php

namespace App\Traits;

use App\Models\Reaction;

trait HasReactions
{

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function getFormattedReactionsAttribute(): array
    {
        return $this->reactions()->get()->toArray();
    }

    public function formattedReactions(): array
    {
        return $this->reactions()->get()->toArray();
    }

    public function addReaction(int $userId, string $emoji): Reaction
    {
        return $this->reactions()->create([
            'user_id' => $userId,
            'emoji' => $emoji,
        ]);
    }

    public function removeReaction(int $userId, string $emoji): bool
    {
        return $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->delete() > 0;
    }

    public function toggleReaction(int $userId, string $emoji): array
    {
        $reaction = $this->reactions()
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();

        if ($reaction) {
            $reaction->delete();
            return ['action' => 'removed', 'reaction' => null];
        }

        $newReaction = $this->addReaction($userId, $emoji);
        return ['action' => 'added', 'reaction' => $newReaction];
    }
}
