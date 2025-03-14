<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ITFolder;
use App\Models\User;

class ITFileSharing extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'uploaded_by',
        'folder_id'
    ];

    /**
     * Get the display name for this file
     * 
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->title;
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the folder this file belongs to
     */
    public function folder()
    {
        return $this->belongsTo(ITFolder::class, 'folder_id');
    }
}