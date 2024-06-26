<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Location;
use App\PlatformAPI\PlatformGateway;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->missing('date')) {
            if (session()->has('appointment-index-date')) {
                $request->merge(['date' => session('appointment-index-date')]);
            } else {
                $request->merge(['date' => now()->toDateString()]);
            }
        }

        session(['appointment-index-date' => $request->get('date')]);

        return Inertia::render('Appointments/Index', [
            'filters' => $request->all('date', 'status', 'staff', 'search', 'location'),
            'visibleLocations' => Location::visibleToUser($request->user())->active()->get()->map(function ($l) { return ['id' => $l->id, 'property_id' => $l->property_id, 'name' => $l->name]; }),
            'appointments' => function () use ($request) {
                return Appointment::with('staff:id,api_id,first_name,last_name')->with([
                    'client' => function ($query) {
                        return $query->select([
                            'id',
                            'api_id',
                            'api_public_id',
                            'first_name',
                            'last_name',
                            'photo_url',
                        ]);
                    },
                ])->with([
                    'client.alerts' => function ($query) {
                        return $query->select(['id', 'client_id', 'staff_id', 'text']);
                    },
                ])->with(['progressNotes' => function ($q) {
                    if (auth()->user()->hasPermissionTo('notes:view-all')) {
                        $q = $q->select(['id','staff_id','is_draft','is_exam','notable_id','notable_type','date_of_service']);
                    } elseif (auth()->user()->hasPermissionTo('notes:view-own')) {
                        $q = $q->select(['id','staff_id','is_draft','is_exam','notable_id','notable_type','date_of_service'])
                            ->where('staff_id', auth()->id());
                    }
                    return $q->with('staff:id,first_name,last_name');
                }])->with('location:id,api_id,name,property_id')
                    ->orderBy('start_date_time', $request->get('order', 'asc'))
                    ->filter($request->only('date', 'status', 'staff', 'search', 'location'))
                    ->paginate();
            },
        ]);
    }

    // This method only updates the notes of an appointment at this time.
    public function update(Request $request, Appointment $appointment)
    {
        $gateway = app(PlatformGateway::class);

        // Update the Appointment in our db.
        $appointment->update(['notes' => $request->note]);

        // Update the appointment in the API.
        $gateway->updateAppointment($appointment->property->api_identifier, $appointment->fresh());

        return back();
    }
}
