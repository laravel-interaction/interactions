<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('subscribe.table_names.subscriptions'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger(config('subscribe.column_names.user_foreign_key'))->index()->comment('user_id');
            $table->morphs('subscribable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('subscribe.table_names.subscriptions'));
    }
}