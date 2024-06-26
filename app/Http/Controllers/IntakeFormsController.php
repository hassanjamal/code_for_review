<?php

namespace App\Http\Controllers;

use App\Events\FormSubmitted;
use App\IntakeForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IntakeFormsController extends Controller
{
    public function create(Request $request)
    {
        $intakeForm = $this->findIntakeFormByCode($request);

        if ($component = $intakeForm->formTemplate->component) {
            return Inertia::render('Forms/' . $component, [
                'code' => $request->get('code'),
            ]);
        }

        return Inertia::render('Forms/Create', [
            'formTemplate' => $intakeForm->formTemplate->schema,
            'code' => $request->get('code'),
        ]);
    }

    public function store(Request $request)
    {
        $intakeForm = $this->findIntakeFormByCode($request);

        try {
            DB::beginTransaction();

            $intakeForm->formSubmission()->create([
                'form_model' => $request->get('model'),
                'signature' => $request->get('signature'),
            ]);

            $kiosk_code = $intakeForm->kiosk_code;

            $this->updateIntakeForm($intakeForm);

            event(new FormSubmitted($intakeForm));

            DB::commit();

            if ($kiosk_code) {
                return redirect()->route('kiosk_form.show');
            }

            return Inertia::render('Forms/Info', [
                'info' => 'Thanks for Submission',
                'info_type' => 'Success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back();
        }
    }

    public function show(IntakeForm $intakeForm)
    {
        if ($intakeForm->submitted) {
            if ($component = $intakeForm->formTemplate->component) {
                return Inertia::render('Forms/' . $component . 'Display', [
                    'form_model' => $intakeForm->formSubmission->form_model,
                    'signature' => $intakeForm->formSubmission->signature,
                ]);
            }
        }

        return Inertia::render('Forms/Info', [
            'info' => 'Form has not been submitted yet',
            'info_type' => 'Warning',
        ]);
    }

    protected function findIntakeFormByCode($request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        return IntakeForm::notSubmitted()->findByCode($request->code)->firstOrFail();
    }

    /**
     * @param $intakeForm
     */
    private function updateIntakeForm($intakeForm): void
    {
        $intakeForm->submitted_at = now();
        $intakeForm->code_expires_at = now();
        $intakeForm->kiosk_code_expires_at = null;
        $intakeForm->kiosk_code = null;

        $intakeForm->save();
    }
}
