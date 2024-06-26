<?php

namespace App\Http\Controllers;

use App\Actions\VerifyMindbodyApiConnectionAction;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Http\Request;

class VerifyMindbodySiteOwnershipController extends Controller
{
    public function __invoke(Request $request, VerifyMindbodyApiConnectionAction $verifyMindbodyApiConnectionAction)
    {
        $data = $request->validate([
            'api_identifier' => ['required'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! $this->validateOwnerCredentials($data)) {
            return back()->withError('Verification failed. Check the site id and your site OWNER credentials.');
        }

        try {
            $property = Property::findByApiIdentifier($request->api_identifier)->first();

            $property->update(['verified_at' => now()]);

            $verifyMindbodyApiConnectionAction->execute($data['api_identifier']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect(route('properties.index'))->with('success', 'Property API connection is verified.');
    }

    protected function validateOwnerCredentials($data):bool
    {
        // In order to verify a site the owner credentials must be verified.
        try {
            $owner = app(PlatformGateway::class)->authenticateStaff($data['api_identifier'], $data['username'], $data['password']);

            return $owner && $owner->api_role ===  'owner';
        } catch (\Exception $e) {
            return false;
        }
    }
}
