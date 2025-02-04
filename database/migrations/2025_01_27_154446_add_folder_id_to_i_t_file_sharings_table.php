<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add folder_id as nullable first
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable();
        });

        // Assign existing files to the 'Uncategorized' folder
        $uncategorizedId = DB::table('i_t_folders')
            ->where('name', 'Uncategorized')
            ->value('id');

        // // Ensure the 'Uncategorized' folder exists
        // if (!$uncategorizedId) {
        //     // Create it if necessary (adjust as needed)
        //     $uncategorizedId = DB::table('i_t_folders')->insertGetId([
        //         'name' => 'Uncategorized',
        //         'description' => 'Default folder for uncategorized files',
        //         'created_by' => 1, // Ensure this user exists
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }

        // Update existing records
        DB::table('i_t_file_sharings')
            ->whereNull('folder_id')
            ->update(['folder_id' => $uncategorizedId]);

        // Change folder_id to non-nullable
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable(false)->change();
        });

        // Add foreign key constraint after making the column non-nullable
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            $table->foreign('folder_id')
                  ->references('id')
                  ->on('i_t_folders')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};