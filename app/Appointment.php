<?php

namespace App;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Appointment extends ApiModel
{
    protected $guarded = [];

    protected $perPage = 25;

    protected $casts = [
        'resources' => 'array',
        'staff_requested' => 'boolean',
        'first_appointment' => 'boolean',
        'is_confirmed' => 'boolean',
        'has_arrived' => 'boolean',
        'cancelled' => 'boolean',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
    ];

    protected $appends = [
        'formatted_time',
        'progress_note_url',
        'formatted_date',
    ];

    /**
     * Accessors & Mutators
     */

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getFormattedTimeAttribute()
    {
        return sprintf('%s-%s', $this->start_date_time->format('M d, g:i'), $this->end_date_time->format('h:i a'));
    }

    public function getFormattedDateAttribute()
    {
        return $this->start_date_time->toFormattedDateString();
    }

    public function getIsCancelledAttribute()
    {
        return $this->cancelled;
    }

    public function getProgressNoteUrlAttribute()
    {
        return route('appointment.progress-notes.create', $this);
    }

    /**
     * Custom Methods
     */
    public function formTemplate()
    {
        $pivot = FormService::forServiceId($this->service_id)->first();

        return $pivot ? $pivot->formTemplate : null;
    }

    /**
     * Relationships
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_api_public_id', 'api_public_id');
    }

    public function progressNotes()
    {
        return $this->morphMany(ProgressNote::class, 'notable');
    }

    public function intakeForm()
    {
        return $this->morphOne(IntakeForm::class, 'formable');
    }

    /**
     * Scopes
     */
    public function scopeForApiId($query, $value)
    {
        return $query->where('api_id', $value);
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where(DB::raw("CONCAT_WS(' ',first_name,last_name)"), 'like', "%" . $search . "%");
            });
        })->when($filters['date'] ?? null, function ($query, $date) {
            $startDate = Carbon::parse($date)->startOfDay();
            $query->whereBetween('start_date_time', [$startDate, $startDate->copy()->endOfDay()]);
        })->when($filters['status'] ?? null, function ($query, $status) {
            switch (strtolower($status)) {
                case 'booked':
                    $query->where(function ($query) {
                        $query->where('status', 'Booked')
                            ->orWhere('status', 'Confirmed');
                    });
                    break;
                case 'arrived':
                    $query->where('status', 'Arrived');
                    break;
                case 'arrived-booked':
                    $query->where(function ($query) {
                        $query->where('status', 'Booked')
                            ->orWhere('status', 'Confirmed')
                            ->orWhere('status', 'Arrived');
                    });
                    break;
                case 'completed':
                    $query->where('status', 'Completed');
                    break;
                case 'no-show':
                    $query->where('status', 'NoShow');
                    break;
            }
        })->when($filters['staff'] ?? null, function ($query, $staffApiId) {
            $query->where('staff_api_id', $staffApiId);
        })->when($filters['location'] ?? null, function ($query, $location) {
            $query->whereHas('location', function ($q) use ($location) {
                $q->where('location_id', $location)->active();
            });
        })->whereHas('location', function ($q) {
            $q->active();
        });
    }
}
