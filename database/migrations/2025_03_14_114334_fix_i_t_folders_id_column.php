<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Check if the ID column is already set as AUTO_INCREMENT PRIMARY KEY
        $columns = DB::select("SHOW COLUMNS FROM i_t_folders WHERE Field = 'id'");
        
        if (!empty($columns) && $columns[0]->Extra !== 'auto_increment') {
            // Step 2: Create a backup of the existing data
            $folders = DB::table('i_t_folders')->get();
            
            // Step 3: Drop the table and recreate it with proper AUTO_INCREMENT
            Schema::dropIfExists('i_t_folders');
            
            Schema::create('i_t_folders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('created_by');
                $table->foreign('created_by')
                      ->references('id')
                      ->on('users');
                $table->timestamps();
                $table->softDeletes();
            });
            
            // Step 4: Reinsert the data with proper IDs
            $counter = 1;
            foreach ($folders as $folder) {
                DB::table('i_t_folders')->insert([
                    'name' => $folder->name,
                    'description' => $folder->description,
                    'created_by' => $folder->created_by,
                    'created_at' => $folder->created_at,
                    'updated_at' => $folder->updated_at,
                    'deleted_at' => $folder->deleted_at,
                ]);
                $counter++;
            }
            
            // Step 5: Update any references to folder IDs in other tables
            if (Schema::hasColumn('i_t_file_sharings', 'folder_id')) {
                // Get all files
                $files = DB::table('i_t_file_sharings')->get();
                
                // Create a mapping of old folder names to new IDs
                $folderMap = [];
                $newFolders = DB::table('i_t_folders')->get();
                foreach ($newFolders as $newFolder) {
                    $folderMap[$newFolder->name] = $newFolder->id;
                }
                
                // Update each file with the correct folder ID
                foreach ($files as $file) {
                    if (isset($file->folder_id) && $file->folder_id == 0) {
                        // Try to find the folder this file was associated with
                        $oldFolder = $folders->where('id', $file->folder_id)->first();
                        if ($oldFolder && isset($folderMap[$oldFolder->name])) {
                            DB::table('i_t_file_sharings')
                                ->where('id', $file->id)
                                ->update(['folder_id' => $folderMap[$oldFolder->name]]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration fixes a structural issue, so down() doesn't need to do anything
        // If we tried to revert this, we'd be putting the database back into a broken state
    }
};
