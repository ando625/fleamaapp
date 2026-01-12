<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users');
            //出品者User
            $table->foreignId('seller_id')->constrained('users');
            //購入者User
            $table->string('status')->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->boolean('buyer_reviewed')->default(false);     // 購入者が評価済みか
            $table->boolean('seller_reviewed')->default(false);    // 出品者が評価済みか
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
        Schema::dropIfExists('transactions');
    }
}
