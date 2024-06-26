<?php

use Illuminate\Support\Facades\Auth;

Route::redirect('/login', '/app/login');
Route::post('/vapor/signed-storage-url', 'Vapor\SignedStorageUrlController@store')->middleware('auth:staff');

Route::group(['prefix' => 'app'], function () {

    /*
     * Login/Logout/Forgot Password Routes.
     * */
    Auth::routes(['register' => false, 'confirm' => false]);

    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')
         ->middleware(['auth:web,staff'])
         ->name('logout');

    /*
     * Routes that require a user to be a guest.
     * Cannot be authenticated here.
     * */
    Route::group(['middleware' => 'guest'], function () {
        Route::post('mindbody-login', 'MindbodyLoginController')->name('login.mindbody');
    });

    /*
     * Routes that require an authenticated user go in this group.
     * Must be authenticated here.
     * */
    Route::group(['middleware' => ['auth:staff,web']], function () {
        Route::get('/', 'DashboardController@index')->name('dashboard');

        /*
         * Routes for properties.
         * */
        Route::group(['middleware' => ['permission:properties:view-all|properties:view-own']], function () {
            Route::get('properties', 'PropertiesController@index')->name('properties.index');
            Route::get('properties/create', 'PropertiesController@create')->name('properties.create');
            Route::get('properties/{property}/edit', 'PropertiesController@edit')->name('properties.edit');
            Route::put('properties/{property}', 'PropertiesController@update')->name('properties.update');
        });

        /*
         * Appointments.
         * */
        Route::group(['middleware' => ['permission:appointments:view-all|appointments:view-own']], function () {
            Route::get('/appointments', 'AppointmentsController@index')->name('appointments.index');
            Route::post('/appointments/{appointment}', 'AppointmentsController@update')->name('appointments.update');
        });

        /*
         * Clients.
         * */
        Route::group(['middleware' => ['permission:clients:view-from-all-properties|clients:view-from-own-property']], function () {
            Route::get('/clients', 'ClientsController@index')->name('clients.index');
            Route::get('/clients/{client}', 'ClientsController@show')->name('clients.show');
            Route::post('/clients/sync', 'ClientsSyncController')->name('clients.sync');
        });

        /*
         * Notes.
         * */
        Route::group(['middleware' => ['permission:notes:view-all|notes:view-own|notes:create']], function () {
            Route::get('progress-notes', 'ProgressNotesController@index')->name('progress-notes.index');
            Route::get('progress-notes/create', 'ProgressNotesController@create')->name('progress-notes.create');
            Route::get('progress-notes/{progressNote}/edit', 'ProgressNotesController@edit')
                 ->name('progress-notes.edit');
            Route::put('progress-notes/{progressNote}', 'ProgressNotesController@update')
                 ->name('progress-notes.update');

            // Manage progress note images.
            Route::post('progress-notes/{progressNote}/images', 'ProgressNoteImagesController@store')
                 ->name('progress-notes.images.store');

            Route::get('appointments/{appointment}/progress-notes/create', 'AppointmentProgressNotesController@create')
                 ->name('appointment.progress-notes.create');
            Route::post('appointments/{appointment}/progress-notes', 'AppointmentProgressNotesController@store')
                 ->name('appointment.progress-notes.store');
            Route::get('appointments/{appointment}/progress-notes/{note}/edit', 'AppointmentProgressNotesController@edit')
                 ->name('appointment.progress-notes.edit');
            Route::put('appointments/{appointment}/progress-notes/{note}/update', 'AppointmentProgressNotesController@update')
                 ->name('appointment.progress-notes.update');
            Route::post('appointments/{appointment}/progress-notes/{note}/sign', 'AppointmentProgressNotesController@sign')
                 ->name('appointment.progress-notes.sign');

            // Note history for the note history viewer
            Route::get('note-history', function () {
                $client = \App\Client::findOrFail(request('clientId'));

                $notes = $client->progressNotes()->with(['notable', 'notable.staff', 'notable.staff.profile', 'images', 'staff', 'staff.profile'])->completed()->paginate();

                return response()->json($notes);
                // $progressNotes = \App\Client::findOrFail(request())
            })->name('note-history');
        });

        /*
         * Image Manager
         * */
        Route::post('image-manager', 'ImageManagerUploadController@store')
             ->middleware(['permission:imageManager:store'])->name('image-manager.store');

        /*
         * Templates..
         * */
        Route::group(['middleware' => ['permission:templates:create|templates:delete']], function () {
            Route::get('/templates', 'TemplatesController@index')->name('templates.index')->middleware('remember');
            Route::get('templates/create', 'TemplatesController@create')->name('templates.create');
            Route::post('templates', 'TemplatesController@store')->name('templates.store');
            Route::get('templates/{template}/edit', 'TemplatesController@edit')->name('templates.edit');
            Route::put('templates/{template}', 'TemplatesController@update')->name('templates.update');
            Route::delete('templates/{template}', 'TemplatesController@destroy')->name('templates.destroy');
        });

        /*
        * Documents..
        * */
        Route::middleware(['permission:documents:view-all|documents:view-own|documents:create'])->group(function () {
            Route::get('clients/{client}/documents/create', 'DocumentsController@create')
                 ->name('clients.documents.create');
            Route::post('clients/{client}/text-documents', 'StoreTextDocumentsController')
                 ->name('clients.text-documents.store');
            Route::post('clients/{client}/file-documents', 'StoreFileDocumentsController')
                 ->name('clients.file-documents.store');

            Route::get('documents/{document}', 'DocumentsController@show')->name('documents.show');
            Route::get('documents/{document}/edit', 'DocumentsController@edit')->name('documents.edit');
            Route::put('documents/{document}', 'DocumentsController@update')->name('documents.update');
        });

        /*
        * Notifications
        * */
        Route::group(['middleware' => []], function () {
            Route::get('/notifications', 'NotificationsController@index')->name('notifications.index');
        });

        /*
        * Settings
        * */
        Route::get('/settings', 'SettingsController')->name('settings');

        /*
        * Roles.
        * */
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', 'RolesController@index')->name('index');
            Route::get('/{role}', 'RolesController@show')->name('show');
        });

        /*
         * Subscriptions.
         * */
        Route::post('location-subscriptions', 'LocationSubscriptionsController@store')
             ->name('location-subscriptions.store');
        Route::post('location-subscriptions/{location}/reactivate', 'LocationSubscriptionsController@reactivate')
             ->name('location-subscriptions.reactivate');
        Route::delete('location-subscriptions/{location}', 'LocationSubscriptionsController@destroy')
             ->name('location-subscriptions.cancel');

        /*
         * Alerts.
         * */
        Route::post('alerts', 'AlertsController@store')->name('alerts.store');
        Route::delete('alerts/{alert}', 'AlertsController@delete')->name('alerts.delete');

        /*
         * Mindbody.
         * */
        Route::post('mindbody-properties', 'CreateMindbodyPropertyController')->name('mindbody.properties.store');
        Route::post('verify-mindbody-ownership', 'VerifyMindbodySiteOwnershipController')
             ->name('mindbody.verify-ownership');

        /*
         * Forms
         */
        Route::get('forms/{intakeForm}', 'IntakeFormsController@show')->name('intake-forms.show');
        Route::post('/clients/{client}/forms/{form}/kiosk-code', 'ClientFormGeneratorController')
             ->name('clients.forms.generate');
    });

    /*
     * Intake Form
     */
    Route::get('forms', 'IntakeFormsController@create')->name('intake-forms.create')->middleware('intake_form');
    Route::post('forms', 'IntakeFormsController@store')->name('intake-forms.store')->middleware('intake_form');

    /*
     * Kiosk form
     */
    Route::get('kiosk', 'KioskFormController@show')->name('kiosk_form.show');
    Route::post('kiosk', 'KioskFormController@store')->name('kiosk_form.store');
});
