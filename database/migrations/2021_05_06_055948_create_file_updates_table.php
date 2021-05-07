<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('scan_id');
            $table->enum('type', ['file', 'directory'])->default("directory");
            $table->string('filename', 50);
            $table->string('path', 200);
            $table->enum('modification_type', ["added", "deleted"])->default("added");
            
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
        Schema::dropIfExists('file_updates');
    }
}
