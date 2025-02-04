<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('i_t_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users');
            $table->timestamps();
            $table->softDeletes(); // Add soft delete support
        });

        // // Create the default "Uncategorized" folder
        // DB::table('i_t_folders')->insert([
        //     'name' => 'Uncategorized',
        //     'description' => 'Default folder for uncategorized files',
        //     'created_by' => 1, // Assuming ID 1 is an admin user
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }

    public function down()
    {
        Schema::dropIfExists('i_t_folders');
    }
};
