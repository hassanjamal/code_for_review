<?php

namespace App\Providers;

use App\Events\FormSent;
use App\Events\FormSubmitted;
use App\Listeners\FormSentAction;
use App\Listeners\FormSubmittedAction;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogPasswordReset;
use App\Listeners\LogRegisteredUser;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            LogRegisteredUser::class,
        ],
        FormSent::class => [
            FormSentAction::class,
        ],
        FormSubmitted::class => [
            FormSubmittedAction::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
        Logout::class => [
            LogSuccessfulLogout::class,
        ],
        PasswordReset::class => [
            LogPasswordReset::class,
        ],
    ];
}
