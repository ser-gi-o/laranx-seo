<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaranxSeoTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laranx_tags', function (Blueprint $table) {
            $table->id();
            $table->string('page')->unique();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('canonical', 2000)->nullable();

            $table->string('feature_image', 2000)->nullable();

            $table->string('og_image', 2000)->nullable();
            $table->string('og_title', 120)->nullable();
            $table->string('og_description', 300)->nullable();

            $table->string('twitter_image', 2000)->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description')->nullable();

            $table->text('jsonld')->nullable();

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
        Schema::dropIfExists('laranxseo_tags');
    }
}
