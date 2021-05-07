<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('scan_id');
            $table->string('file_path', 200);
            $table->longText('base_version_content');
            $table->longText('compared_version_content');
            //indeces and contraints
            $table->foreign('scan_id')->references('id')->on('scans');
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
        Schema::dropIfExists('content_updates');
    }
}
