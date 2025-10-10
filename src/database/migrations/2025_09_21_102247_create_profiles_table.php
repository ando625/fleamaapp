<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // user_id カラム作成＋外部キー＋ユニーク制約＋削除時連鎖
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('profile_image')->nullable();
            $table->string('postal_code');
            $table->string('address');
            $table->string('building')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
