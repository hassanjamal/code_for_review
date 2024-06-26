<?php

namespace Tests\Feature;

use App\Actions\SyncAppointmentsToPropertyAction;
use App\Appointment;
use App\Client;
use App\FormService;
use App\FormSubmission;
use App\FormTemplate;
use App\Http\Middleware\IntakeFormMiddleware;
use App\IntakeForm;
use App\Mail\IntakeFormForClient;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class IntakeFormTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function intake_form_url_is_publicly_accessible()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();

        $this->get($intakeForm->link)->assertOk();
    }

    /** @test */
    public function it_aborts_if_link_is_expired()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create([]);
        $intakeForm->code_expires_at = now()->subDay();
        $intakeForm->save();

        $request = Request::create($intakeForm->link, 'GET');
        $middleware = new IntakeFormMiddleware();

        try {
            $middleware->handle($request, function () {});
        } catch (\Throwable $e) {
            $this->assertEquals(
                new NotFoundHttpException(), $e
            );
        }
    }

    /** @test */
    public function it_aborts_if_code_does_not_exists_db()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->create();
        $request = Request::create($intakeForm->link . 'some-invalid-code', 'GET');
        $middleware = new IntakeFormMiddleware();

        try {
            $middleware->handle($request, function () {
            });
        } catch (\Throwable $e) {
            $this->assertEquals(
                new NotFoundHttpException(), $e
            );
        }
    }

    /** @test */
    public function it_aborts_while_making_a_get_request_for_form_if_form_has_been_submitted()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->state('submitted')->create([
            'submitted_at' => now()->subDay(),
        ]);
        $this->get($intakeForm->link)
             ->assertStatus(404);
    }

    /** @test */
    public function it_displays_the_component_form_based_on_ref_code_to_unauthenticated_user()
    {
        $this->createTenant();
        $form = factory(FormTemplate::class)->create();
        $intakeForm = factory(IntakeForm::class)->create(
            [
                'form_template_id' => $form->id,
            ]
        );

        $this->assertTrue(auth()->guest());

        $this->get($intakeForm->link)
             ->assertOk()
             ->assertComponentIs('Forms/MA/IntakeForm')
             ->assertPropValue('code', $intakeForm->code);
    }

    /** @test */
    public function it_displays_the_schema_form_based_on_ref_code_to_unauthenticated_user()
    {
        $this->createTenant();
        $form = factory(FormTemplate::class)->create(['component' => null]);
        $intakeForm = factory(IntakeForm::class)->create(
            [
                'form_template_id' => $form->id,
            ]
        );

        $this->assertTrue(auth()->guest());

        $this->get($intakeForm->link)
             ->assertOk()
             ->assertComponentIs('Forms/Create')
             ->assertPropValue('formTemplate', $intakeForm->formTemplate->schema)
             ->assertPropValue('code', $intakeForm->code);
    }

    /** @test */
    public function it_stores_the_form_submission_for_unauthenticated_user()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();

        $this->assertTrue(auth()->guest());

        $this->post(routeForTenant('intake-forms.store'),
            [
                'code' => $intakeForm->code,
                'model' => [
                    'name' => 'foo',
                    'age' => 25,
                    'email' => 'foo@example.com',
                ],
                'signature' => 'some-signature',
            ]
        );

        $intakeForm->refresh();

        $formSubmission = FormSubmission::where('intake_form_id', $intakeForm->id)->first();
        $this->assertEquals(1, $formSubmission->count());
        $this->assertEquals([
            'name' => 'foo',
            'age' => 25,
            'email' => 'foo@example.com',
        ], $formSubmission->form_model);
    }

    /** @test */
    public function it_sets_submitted_and_submitted_time_once_form_submission_is_complete()
    {
        $this->createTenant();

        Carbon::setTestNow('Jan. 2nd 12:34 pm');

        $intakeForm = factory(IntakeForm::class)->create(['submitted_at' => null]);

        $this->assertNull($intakeForm->submitted_at);

        $this->assertTrue(auth()->guest());

        $this->post($intakeForm->link, [
            'code' => $intakeForm->code,
            'model' => [
                'name' => 'foo',
                'age' => 25,
                'color' => 'red',
                'email' => 'foo@example.com',
            ],
        ]);

        $intakeForm->refresh();

        $this->assertTrue($intakeForm->submitted);
    }

    /** @test */
    public function it_aborts_if_submitted_form_is_re_submitted()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->state('submitted')->create([
            'submitted_at' => now()->subDay(),
            'code_expires_at' => now()->subDay(),
        ]);

        $this->post($intakeForm->link, [
            'code' => $intakeForm->code,
            'model' => [
                'name' => 'foo',
                'age' => 25,
                'color' => 'red',
                'email' => 'foo@example.com',
            ],
        ])->assertStatus(404);
    }

    /** @test */
    public function the_show_page_is_not_viewable_by_guests()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->create();
        $formSubmission = factory(FormSubmission::class)->create([
            'intake_form_id' => $intakeForm->id,
            'form_model' => [
                'name' => 'foo',
                'age' => 25,
                'color' => 'red',
                'email' => 'foo@example.com',
            ],
        ]);

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('intake-forms.show', [$intakeForm]))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function it_displays_the_form_submitted_to_authenticated_user()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();
        $intakeForm = factory(IntakeForm::class)->state('submitted')->create([
            'submitted_at' => now()->subDay(),
        ]);
        $formSubmission = factory(FormSubmission::class)->create([
            'intake_form_id' => $intakeForm->id,
            'form_model' => [
                'name' => 'foo',
                'age' => 25,
                'color' => 'red',
                'email' => 'foo@example.com',
            ],
            'signature' => 'some-random-string-for-signature',
        ]);

        $this->actingAs($user)
             ->get(routeForTenant('intake-forms.show', [$intakeForm]))
             ->assertOk()
             ->assertComponentIs('Forms/MA/IntakeFormDisplay')
             ->assertPropValue('form_model', [
                 'name' => 'foo',
                 'age' => 25,
                 'color' => 'red',
                 'email' => 'foo@example.com',
             ]);
    }

    /** @test */
    public function it_sends_a_mail_to_client_with_intake_form_link_when_a_new_first_appointments_is_created()
    {
        Mail::fake();

        $this->createTenant();

        $property = factory(Property::class)->create();

        $client = factory(Client::class)->create(['api_public_id' => 'hassan_1']);
        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
            'service_id' => 5,
        ]);

        $this->assertCount(0, Appointment::all());

        app(SyncAppointmentsToPropertyAction::class)
            ->execute($property->id, Carbon::parse('2019-11-20T18:00:00'), Carbon::parse('2019-11-20T18:20:00'));

        $appointments = Appointment::with('intakeForm')->get();
        $this->assertGreaterThan(0, $appointments->count());

        $ids = $appointments->pluck('api_id');
        $this->assertContains('70510', $ids);

        Mail::assertSent(IntakeFormForClient::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email);
        });

        $intakeForm = $appointments->first()->intakeForm;

        $this->assertTrue($form->is($intakeForm->formTemplate));
        $this->assertTrue($client->is($intakeForm->client));

        app(SyncAppointmentsToPropertyAction::class)
            ->execute($property->id, Carbon::parse('2019-11-20T18:00:00'), Carbon::parse('2019-11-20T18:20:00'));
    }

    /** @test */
    public function intake_form_code_must_be_a_string_and_is_required_on_create()
    {
        $this->createTenant();

        $this->get(routeForTenant('intake-forms.create', ['code' => null]))
            ->assertSessionHasErrors('code');

        $this->get(routeForTenant('intake-forms.create', ['code' => ['not-a-string']]))
            ->assertSessionHasErrors('code');
    }

    /** @test */
    public function it_creates_a_new_intake_form_with_kiosk_code_and_set_its_expiration()
    {
        $this->createTenant();
        Carbon::setTestNow(now()->midDay());
        $staff = factory(Staff::class)->state('staff')->create();

        $client = factory(Client::class)->create(['property_id' => $staff->property_id]);
        $form = factory(FormTemplate::class)->create();

        $this->actingAs($staff)
            ->from(routeForTenant('clients.show', $client))
            ->post(routeForTenant('clients.forms.generate', ['client' => $client->id,'form' => $form->id]))
            ->assertRedirect(routeForTenant('clients.show', $client));

        $intakeForms = IntakeForm::get();

        $this->assertCount(1, $intakeForms);
        $this->assertNotNull($intakeForms->first()->kiosk_code);
        $this->assertEquals(now()->addHours(5), $intakeForms->first()->kiosk_code_expires_at);
    }

    /** @test */
    public function it_displays_success_if_form_is_submitted_without_kiosk_code()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();

        $this->assertNull($intakeForm->submitted_at);

        $this->assertTrue(auth()->guest());

        $this->post(routeForTenant('intake-forms.store'),
            [
                'code' => $intakeForm->code,
                'model' => [
                    'name' => 'foo',
                    'age' => 25,
                    'email' => 'foo@example.com',
                ],
                'signature' => 'some-signature',
            ]
        )->assertOk()
            ->assertPropValue('info', function ($prop) {
                $this->assertEquals('Thanks for Submission', $prop);
            })
            ->assertPropValue('info_type', function ($prop) {
                $this->assertEquals('Success', $prop);
            })
            ->assertComponentIs('Forms/Info');

        $intakeForm->refresh();

        $this->assertTrue($intakeForm->isExpired);
        $this->assertTrue($intakeForm->submitted);
        $this->assertNull($intakeForm->kiosk_code);
        $this->assertNull($intakeForm->kiosk_code_expires_at);
    }

    /** @test */
    public function it_redirects_to_kiosk_if_form_is_submitted_with_kiosk_code()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->state('withKioskCode')->create();

        $this->post(routeForTenant('intake-forms.store'),
            [
                'code' => $intakeForm->code,
                'model' => [
                    'name' => 'foo',
                    'age' => 25,
                    'email' => 'foo@example.com',
                ],
                'signature' => 'some-signature',
            ]
        )->assertRedirect(routeForTenant('kiosk_form.show'));

        $intakeForm->refresh();

        $this->assertTrue($intakeForm->isExpired);
        $this->assertTrue($intakeForm->submitted);
        $this->assertNull($intakeForm->kiosk_code);
        $this->assertNull($intakeForm->kiosk_code_expires_at);
    }
}
