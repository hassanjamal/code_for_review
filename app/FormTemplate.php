<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'schema' => 'array',
    ];

    public function intakeForm()
    {
        return $this->hasMany(IntakeForm::class);
    }

    public function formServices()
    {
        return $this->hasMany(FormService::class);
    }
}
