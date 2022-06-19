<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('content')->default('');
            $table->string('mentioned_users')->default('');
            $table->integer('likes_number')->default(0);
            $table->integer('comments_number')->default(0);
            $table->integer('shares_number')->default(0);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->unsignedBigInteger('parent_post_id')->nullable();
            $table->foreign('parent_post_id')->references('id')->on('posts');
            $table->unsignedBigInteger('shared_post_id')->nullable();
            $table->foreign('shared_post_id')->references('id')->on('posts');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
