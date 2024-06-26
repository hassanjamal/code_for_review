<?php

Route::get('/', function () {
    return \Inertia\Inertia::render('Home');
})->name('home');

Route::get('register', 'Auth\RegisterController@index')->name('register');
Route::post('register', 'Auth\RegisterController@store');

/*
 * Mindbody Webhook Routes
 */
Route::get('/mindbody/webhooks', function () {
    return response()->json();
});
Route::post('/mindbody/webhooks', 'Webhooks\MindbodyWebhooksController@handleWebhook')->name('mindbody.webhooks');
