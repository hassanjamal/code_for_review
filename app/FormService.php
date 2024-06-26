<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormService extends Model
{
    protected $guarded = [];

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class);
    }

    public function scopeForServiceId($query, $id)
    {
        return $query->where('service_id', $id);
    }
}
