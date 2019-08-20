<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Exchange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exchange_rates_id');
            $table->string('forexBuying')->nullable();
            $table->string('forexSelling')->nullable();
            $table->string('banknoteBuying')->nullable();
            $table->string('banknoteSelling')->nullable();
            $table->foreign('exchange_rates_id')->references('id')->on('exchange_rates')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
