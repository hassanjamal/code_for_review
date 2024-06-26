<?php

if (! function_exists('inertia_request')) {
    function inertia_request()
    {
        // Determines if the request if from InertiaJs.
        return (request()->header('x-inertia') === 'true');
    }
}

if (! function_exists('routeForTenant')) {
    function routeForTenant($routeName, $params = [])
    {
        if (! tenant()) {
            // When there is no tenant, lets just return the base route.
            return route($routeName, $params);
        }

        return sprintf('%s://%s%s', parse_url(config('app.url'))['scheme'], tenant()->domains[0], route($routeName, $params, false));
    }
}

if (! function_exists('makeDoubleCompositeKey')) {
    function makeDoubleCompositeKey($propertyId, $clientApiId)
    {
        return sprintf('%s:%s', $propertyId, $clientApiId);
    }
}

if (! function_exists('makeTripleCompositeKey')) {
    function makeTripleCompositeKey($propertyId, $locationApiId, $appointmentApiId)
    {
        return sprintf('%s:%s:%s', $propertyId, $locationApiId, $appointmentApiId);
    }
}
