<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create IT Basic Info folder
        DB::table('i_t_folders')->insert([
            'name' => 'IT Basic Info',
            'description' => 'Basic IT information and documentation',
            'created_by' => 1, // Assuming ID 1 is an admin user
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the ID of the IT Basic Info folder
        $itBasicInfoId = DB::table('i_t_folders')
            ->where('name', 'IT Basic Info')
            ->value('id');

        // Move existing files to IT Basic Info folder (except those already in Uncategorized)
        // if ($itBasicInfoId) {
        //     DB::table('i_t_file_sharings')
        //         ->whereNull('folder_id')
        //         ->update(['folder_id' => $itBasicInfoId]);
        // }
    }

    public function down()
    {
        // Get the ID of the Uncategorized folder
        // $uncategorizedId = DB::table('i_t_folders')
        //     ->where('name', 'Uncategorized')
        //     ->value('id');

        // // Move files back to Uncategorized
        // if ($uncategorizedId) {
        //     $itBasicInfoId = DB::table('i_t_folders')
        //         ->where('name', 'IT Basic Info')
        //         ->value('id');

        //     if ($itBasicInfoId) {
        //         DB::table('i_t_file_sharings')
        //             ->where('folder_id', $itBasicInfoId)
        //             ->update(['folder_id' => $uncategorizedId]);
        //     }
        // }

        // Delete the IT Basic Info folder
        DB::table('i_t_folders')
            ->where('name', 'IT Basic Info')
            ->delete();
    }
};
