<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at', 'ends_at',
        'created_at', 'updated_at',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
    }
}
