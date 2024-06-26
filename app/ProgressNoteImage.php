<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProgressNoteImage extends Model
{
    protected $guarded = [];

    protected $appends = [
        'signed_url',
    ];

    /**
     * Accessors & Mutators
     */
    public function getSignedUrlAttribute()
    {
        return Storage::temporaryUrl('progress-note-images/'.$this->key, now()->addMinutes(60));
    }
    /**
     * Relationships
     */
    public function progressNote()
    {
        return $this->belongsTo(ProgressNote::class);
    }
}
