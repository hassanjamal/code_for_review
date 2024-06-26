<?php

namespace App\Http\Controllers;

use App\Alert;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', Alert::class);

        $request->validate([
            'text' => 'required',
            'clientId' => 'required',
        ]);

        Alert::create([
            'text' => $request->get('text'),
            'staff_id' => auth()->id(),
            'client_id' => $request->get('clientId'),
        ]);

        return back();
    }

    public function delete(Alert $alert)
    {
        $alert->delete();

        return back();
    }
}
