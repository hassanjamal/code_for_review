<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProgressNote extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected $perPage = 1;

    protected static $logOnlyDirty = true;

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logName = 'progress-notes';

    protected $casts = [
        'is_draft' => 'boolean',
        'is_exam' => 'boolean',
    ];

    protected $dates = [
        'date_of_service',
    ];

    protected $appends = [
        'formatted_date_of_service',
    ];


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('viewable', function (Builder $query) {
            $user = auth()->user();

            if (! $user || $user->can('notes:view-all')) {
                return $query;
            }

            if ($user->can('notes:view-own')) {
                return $query->where('staff_id', auth()->id());
            }

            return $query->where('id', -1);
        });
    }

    /**
     * Custom Functions
     */

    /**
     * Accessors & Mutators
     */
    public function getFormattedDateOfServiceAttribute()
    {
        if (! $this->date_of_service) {
            return null;
        }

        return $this->date_of_service->format('M d, Y');
    }

    public function getContentAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }
    }

    public function setContentAttribute($value)
    {
        if ($value) {
            $this->attributes['content'] = encrypt($value);
        }
    }

    public function getMetaAttribute($value)
    {
        if ($value) {
            return json_decode(decrypt($value), true);
        }
    }

    public function setMetaAttribute($value)
    {
        if ($value) {
            $this->attributes['meta'] = encrypt(json_encode($value));
        }
    }



    /**
     * Relationships
     */
    public function notable()
    {
        // Appointments, Classes, Null.
        return $this->morphTo();
    }

    public function images()
    {
        return $this->hasMany(ProgressNoteImage::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Query Scopes
     */
    public function scopeForStaff($query, Staff $staff)
    {
        return $query->where('staff_id', $staff->id);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_draft', false);
    }
}
