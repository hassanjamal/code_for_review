<?php

namespace App\Http\Controllers;

use App\IntakeForm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KioskFormController extends Controller
{
    public function show()
    {
        return Inertia::render('Forms/Kiosk/Create', []);
    }

    public function store(Request $request) // submitting the kiosk code
    {
        $intakeForm = $this->findIntakeFormByCode($request);

        if ($intakeForm && ! $intakeForm->isKioskCodeExpired) {
            $intakeForm->kiosk_code_expires_at = now()->addSeconds(10);

            $intakeForm->save();

            if ($component = $intakeForm->formTemplate->component) {
                return Inertia::render('Forms/' . $component, [
                    'code' => $intakeForm->code,
                ]);
            }
        }

        return redirect()->back()->withErrors(['invalidCode' => "Invalid code"]);
    }

    protected function findIntakeFormByCode($request)
    {
        $request->validate([
            'kioskCode' => 'required|string',
        ]);

        return IntakeForm::findByKioskCode($request->kioskCode)->first();
    }
}
