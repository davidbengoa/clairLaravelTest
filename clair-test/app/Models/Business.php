<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Business
 *
 * @mixin Builder
 */
class Business extends Model
{
    use HasFactory;

    /**
     * The users that belong to the business
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users');
    }
}
