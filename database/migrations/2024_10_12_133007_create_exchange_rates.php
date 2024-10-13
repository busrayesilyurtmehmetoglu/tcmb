<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('usd_s', 10, 4)->nullable();
            $table->decimal('usd_a', 10, 4)->nullable();
            $table->decimal('eur_s', 10, 4)->nullable();
            $table->decimal('eur_a', 10, 4)->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
};
