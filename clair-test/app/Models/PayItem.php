<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * PayItem
 *
 * @mixin Builder
 */
class PayItem extends Model
{
    use HasFactory;

    public function findPayItem($externalId, $userId, $businessId): Builder|Model|null
    {
        return PayItem::query()
            ->where('external_id', $externalId)
            ->where('user_id', $userId)
            ->where('business_id', $businessId)
            ->first();
    }

    public function deleteOldPayItems($externalIds)
    {
        PayItem::query()
            ->whereNotIn('external_id', $externalIds)
            ->delete();
    }

    public function toUser() : BelongsTo {
        return $this->belongsTo();
    }
}
