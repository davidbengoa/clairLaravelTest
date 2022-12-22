<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedDecimal('amount')->nullable(false)->default(0);
            $table->decimal('worked_hours')->nullable(false)->default(0);
            $table->unsignedDecimal('pay_rate')->nullable(false)->default(0);
            $table->date('pay_date')->nullable(false);
            $table->string('external_id')->nullable(false);
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_items');
    }
};
