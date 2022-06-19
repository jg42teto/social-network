<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_posts', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->foreignId('post_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->boolean('repost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_posts');
    }
}
