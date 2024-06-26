<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Template extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected static $logOnlyDirty = true;

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logName = 'templates';

    public function creator()
    {
        return $this->morphTo();
    }

    public function getDefaultGroupNameAttribute($value)
    {
        return strtoupper($value);
    }
}
