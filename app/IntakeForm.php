<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntakeForm extends Model
{
    protected $guarded = [];

    protected $appends = [
        'form_status',
        'formatted_date',
        'available_kiosk_code',
        'name',
    ];

    protected $casts = [
        'submitted' => 'boolean',
        'submitted_at' => 'datetime',
        'kiosk_code_expires_at' => 'datetime',
        'code_expires_at' => 'datetime',
    ];

    /**
     * Accessors & Mutators
     */
    public function getNameAttribute()
    {
        return $this->formTemplate->name;
    }

    public function getSubmittedAttribute()
    {
        return !is_null($this->submitted_at);
    }

    public function getLinkAttribute()
    {
        return routeForTenant('intake-forms.create') . '?code=' . $this->code;
    }

    public function getIsExpiredAttribute()
    {
        return $this->code_expires_at->lt(now());
    }

    public function getIsKioskCodeExpiredAttribute()
    {
        return $this->kiosk_code_expires_at->lt(now());
    }

    public function getFormStatusAttribute()
    {
        if ($this->submitted) {
            return 'Submitted';
        } elseif ($this->isExpired || $this->isKioskCodeExpired) {
            return 'Expired';
        }

        return 'Pending';
    }

    public function getFormattedDateAttribute()
    {
        if ($this->form_status === 'Submitted') {
            return $this->submitted_at->format('F jS, h:i A');
        }

        return $this->created_at->format('F jS, h:i A');
    }

    public function getAvailableKioskCodeAttribute()
    {
        if ($this->kiosk_code && !$this->isKioskCodeExpired) {
            return $this->kiosk_code;
        }
    }


    /**
     * Relationships
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class);
    }

    public function formSubmission()
    {
        return $this->hasOne(FormSubmission::class);
    }

    public function formable()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeFindByCode($query, $code)
    {
        return $query->whereCode($code);
    }

    public function scopeNotSubmitted($query)
    {
        return $query->whereNull('submitted_at');
    }

    public function scopeFindByKioskCode($query, $code)
    {
        return $query->whereKioskCode($code);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('submitted_at')
                     ->orWhere('code_expires_at', '<=', now())
                     ->orWhere('kiosk_code_expires_at', '<=', now());
    }
}
