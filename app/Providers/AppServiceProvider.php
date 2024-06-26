<?php

namespace App\Providers;

use App\Alert;
use App\Appointment;
use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\IntakeForm;
use App\PlatformAPI\Mindbody\MindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\ProgressNote;
use App\Staff;
use App\Template;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\TenantManager;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // This code enables the cache for tenancy package.
        tenancy()->hook('bootstrapped', function (TenantManager $tenantManager) {
            PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $tenantManager->getTenant('id');
            Inertia::share([
                'logos' => [
                    'quickernotes_logo_white' => global_asset('/img/quickernotes_logo_white.svg'),
                    'quickernotes_logo' => global_asset('/img/quickernotes_logo.svg'),
                ],
            ]);
        });

        Relation::morphMap([
            'appointments' => Appointment::class,
            'staff' => Staff::class,
            'alerts' => Alert::class,
            'progress-notes' => ProgressNote::class,
            'intake-forms' => IntakeForm::class,
            'templates' => Template::class,
        ]);


        Inertia::share([
            'csrf_token' => csrf_token(),
        ]);
    }

    public function register()
    {
        $this->registerInertia();
        //$this->registerGlide();
        $this->registerLengthAwarePaginator();

        Route::macro('forTenant', function ($routeName, $params = []) {
            return routeForTenant($routeName, $params);
        });

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        $this->app->bind(PlatformGateway::class, MindbodyGateway::class);
    }

    public function registerInertia()
    {
        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share('auth.user', function () {
            if (Auth::user()) {
                return [
                    'id' => Auth::user()->id,
                    'api_id' => Auth::user()->api_id,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'initials' => Auth::user()->initials,
                    'business_name' => ucfirst(tenant()->name),
                    'permissions' => Auth::user()->getAllPermissions()->pluck('name'),
                    'nav_permissions' => [
                        'properties' => optional(auth()->user())->hasAnyPermission([
                            'properties:view-all',
                            'properties:view-own',
                        ]),
                        'appointments' => optional(auth()->user())->hasAnyPermission([
                            'appointments:view-all',
                            'appointments:view-own',
                        ]),
                        'clients' => optional(auth()->user())->hasAnyPermission([
                            'clients:view-from-own-property',
                            'clients:view-from-all-properties',
                        ]),
                        'notes' => optional(auth()->user())->hasAnyPermission([
                            'notes:view-all',
                            'notes:view-own',
                        ]),
                        'templates' => optional(auth()->user())->hasAnyPermission([
                            'templates:create',
                        ]),
                        'roles' => optional(auth()->user())->hasAnyPermission([
                            'roles:assign',
                            'roles:create',
                            'roles:delete',
                        ]),
                    ],
                ];
            }
        });

        Inertia::share([
            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'info' => Session::get('info'),
                    'error' => Session::get('error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors') ? Session::get('errors')->getBag('default')->getMessages() : (object) [];
            },
        ]);
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data' => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(Request::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url' => $this->previousPageUrl(),
                        'label' => 'Previous',
                        'active' => false,
                    ])->push([
                        'url' => $this->nextPageUrl(),
                        'label' => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }
}
