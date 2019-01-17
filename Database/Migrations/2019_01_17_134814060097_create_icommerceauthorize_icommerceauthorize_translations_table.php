<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcommerceauthorizeIcommerceAuthorizeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icommerceauthorize__icommerceauthorize_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Your translatable fields

            $table->integer('icommerceauthorize_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['icommerceauthorize_id', 'locale']);
            $table->foreign('icommerceauthorize_id')->references('id')->on('icommerceauthorize__icommerceauthorizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('icommerceauthorize__icommerceauthorize_translations', function (Blueprint $table) {
            $table->dropForeign(['icommerceauthorize_id']);
        });
        Schema::dropIfExists('icommerceauthorize__icommerceauthorize_translations');
    }
}
