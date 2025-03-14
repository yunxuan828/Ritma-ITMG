<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ITFolder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    /**
     * Get the display name for this folder
     * 
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

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
