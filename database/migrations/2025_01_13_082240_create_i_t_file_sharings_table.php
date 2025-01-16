<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('i_t_file_sharings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->unsignedInteger('uploaded_by');
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('i_t_file_sharings');
    }
};