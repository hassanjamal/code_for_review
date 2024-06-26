<?php

namespace App;

use App\Traits\CreatesTemplates;
use App\Traits\HasName;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

class Staff extends AuthenticatableApiModel
{
    use HasRoles, CausesActivity, CreatesTemplates, HasName;

    protected $guard = 'staff';

    protected $guarded = [];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'initials',
    ];

    /**
     * Custom Functions
     */

    /**
     * Accessors & Mutators
     */

    /**
     * Relationships
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'staff_api_id', 'api_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'staff_id', 'id');
    }

    public function progressNotes()
    {
        return $this->hasMany(ProgressNote::class);
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable', 'profileable_type', 'profileable_id', 'id')
            ->withDefault();
    }

    /**
     * Query Scopes
     */
    public function scopeForApiId($query, $value)
    {
        return $query->where('api_id', $value);
    }

    public function scopeVisibleLocations($query)
    {
        return $query->whereHas('property.locations', function ($q) {
            $q->active();
        });
    }
}
