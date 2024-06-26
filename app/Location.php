<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'active' => 'boolean',
    ];

    protected $appends = [
        'is_subscribed',
    ];

    public function getIsActiveAttribute()
    {
        return $this->active;
    }

    public function getIsSubscribedAttribute()
    {
        return $this->subscription !== null;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->current();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'location_api_id', 'api_id');
    }

    /**
     * Query Scopes.
     */

    public function scopeForApiId($query, $value)
    {
        return $query->where('api_id', $value);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('subscription', function ($q) {
            $q;
        });
    }

    public function scopeVisibleToUser($query, $user)
    {
        if ($user instanceof User) {
            return $query;
        }

        if ($user->hasPermissionTo('properties:view-all', 'staff')) {
            return $query;
        }

        return $query->where('property_id', $user->property_id);
    }
}
