<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ITFolder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    /**
     * Get the files in this folder
     */
    public function files()
    {
        return $this->hasMany(ITFileSharing::class, 'folder_id');
    }

    /**
     * Get the user who created the folder
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the folder can be deleted
     */
    public function canDelete()
    {
        return $this->files()->count() === 0;
    }
}
