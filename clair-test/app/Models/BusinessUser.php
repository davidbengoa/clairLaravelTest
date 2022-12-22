<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BusinessUser
 *
 * @mixin Builder
 */
class BusinessUser extends Model
{
    use HasFactory;

    public function findByUserExternalId($externalId): Builder|Model|null
    {
        return BusinessUser::query()->where('external_user_id', $externalId)->first();
    }
}
