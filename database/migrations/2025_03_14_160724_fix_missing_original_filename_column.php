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
        // Check if the column doesn't exist
        if (!Schema::hasColumn('i_t_file_sharings', 'original_filename')) {
            Schema::table('i_t_file_sharings', function (Blueprint $table) {
                $table->string('original_filename')->nullable()->after('file_path');
            });
            
            // Extract filename from file_path for existing records
            DB::statement("UPDATE i_t_file_sharings SET original_filename = SUBSTRING_INDEX(file_path, '/', -1) WHERE original_filename IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('i_t_file_sharings', 'original_filename')) {
            Schema::table('i_t_file_sharings', function (Blueprint $table) {
                $table->dropColumn('original_filename');
            });
        }
    }
};
