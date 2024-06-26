<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Client extends ApiModel
{
    protected $guarded = [];

    protected $perPage = 20;

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $dates = [
        'first_appointment_date',
        'birth_date',
    ];

    protected $appends = [
        'full_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('viewable', function (Builder $query) {
            $user = auth()->user();

            if (! $user || $user->can('clients:view-from-all-properties')) {
                return $query;
            }

            if ($user->can('clients:view-from-own-property')) {
                return $query->where('property_id', auth()->user()->property_id);
            }

            return $query->where('id', -1);
        });
    }



    /**
     * Accessors & Mutators
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getEmailAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }
    }

    public function setEmailAttribute($value)
    {
        if ($value) {
            $this->attributes['email'] = encrypt($value);
        }
    }

    /**
     * Relationships
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'client_api_public_id', 'api_public_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'client_id', 'id');
    }

    public function progressNotes()
    {
        return $this->hasMany(ProgressNote::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function intakeForms()
    {
        return $this->hasMany(IntakeForm::class);
    }

    /**
     * Query Scopes
     */
    public function scopeForApiId($query, $value)
    {
        return $query->where('api_id', $value);
    }

    public function scopeForApiPublicId($query, $value)
    {
        return $query->where('api_public_id', $value);
    }

    public function getIsActiveAttribute()
    {
        return $this->active;
    }

    public function setMergedAtAttribute($value)
    {
        $this->attributes['merged_at'] = $value ? Carbon::create($value) : $value;
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(\DB::raw("CONCAT_WS(' ',first_name,last_name)"), 'like', "%" . $search . "%");
        })->when($filters['property'] ?? null, function ($query, $property) {
            $query->where('property_id', $property);
        });
    }
}
