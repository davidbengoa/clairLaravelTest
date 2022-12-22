<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\PayItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use function Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $businesses = Business::factory(5)->create();
        User::factory(10)->create();

        User::all()->each(function ($user) use ($businesses) {
            $businesses->random(rand(1, $businesses->count()))
                ->pluck('id')->each(function ($businessId) use ($user) {
                    $user->businesses()->attach($businessId, ['external_user_id' => Str::random(10)]);

                    PayItem::create([
                        'amount' => random_int(1,100),
                        'worked_hours' => random_int(1,100) / 10,
                        'pay_rate' => random_int(1,100) / 10,
                        'pay_date' => now(),
                        'external_id' => Str::random(10),
                        'user_id' => $user->id,
                        'business_id' => $businessId,
                    ]);
                });
        });

//        BusinessUser::factory(10)->create();
//        PayItem::factory(20)->create();
    }
}
