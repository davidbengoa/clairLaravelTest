<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PayItem
 *
 * @mixin Builder
 */
class PayItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'worked_hours',
        'pay_rate',
        'pay_date',
        'external_id',
        'user_id',
        'business_id',
    ];

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

}
