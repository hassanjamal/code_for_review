<?php

namespace Tests\Unit;

use App\FormService;
use App\FormTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_form()
    {
        $this->createTenant();

        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
        ]);


        $this->assertTrue($formService->is($form->formServices[0]));
    }
}
