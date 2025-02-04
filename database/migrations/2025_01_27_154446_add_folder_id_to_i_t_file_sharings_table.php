<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            // First add the folder_id column as nullable
            $table->unsignedBigInteger('folder_id')->nullable();
            
            // Add foreign key constraint
            $table->foreign('folder_id')
                  ->references('id')
                  ->on('i_t_folders')
                  ->onDelete('restrict'); // Prevent deletion of folders with files
        });

        // Now update existing records to use the default "Uncategorized" folder
        // $uncategorizedId = DB::table('i_t_folders')
        //     ->where('name', 'Uncategorized')
        //     ->value('id');
            
        // if ($uncategorizedId) {
        //     DB::table('i_t_file_sharings')
        //         ->whereNull('folder_id')
        //         ->update(['folder_id' => $uncategorizedId]);
        // }

        // Finally make the column required
        Schema::table('i_t_file_sharings', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable(false)->change();
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
