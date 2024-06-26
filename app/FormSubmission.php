<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $guarded = [];

    protected $appends = [
        'formatted_created_at',
    ];



    /**
     * Accessors & Mutators
     */
    public function getSignatureAttribute($signature)
    {
        return  gzuncompress(base64_decode($signature));
    }

    public function setSignatureAttribute($signature)
    {
        $this->attributes['signature'] = base64_encode(gzcompress($signature, 9));
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function getFormModelAttribute($value)
    {
        if ($value) {
            return json_decode(decrypt($value), true);
        }
    }

    public function setFormModelAttribute($value)
    {
        if ($value) {
            $this->attributes['form_model'] = encrypt(json_encode($value));
        }
    }

    /**
     * Relationships
     */
    public function intakeForm()
    {
        return $this->belongsTo(IntakeForm::class);
    }

    public function client()
    {
        return $this->intakeForm->client;
    }
}
