<?php

namespace Tests\Unit;

use App\Location;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_have_many_locations()
    {
        $this->createTenant();

        $property = factory(Property::class)->state('no-locations')->create();
        $locations = factory(Location::class, 2)->create(['property_id' => $property->id]);

        $this->assertTrue($locations[0]->is($property->fresh()->locations[0]));
        $this->assertTrue($locations[1]->is($property->fresh()->locations[1]));
    }

    /** @test */
    public function it_can_have_many_staff_members()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $staffA = factory(Staff::class)->create(['api_id' => 100, 'property_id' => $property->id]);
        $staffB = factory(Staff::class)->create(['api_id' => 200, 'property_id' => $property->id]);

        $this->assertTrue($staffA->is($property->staff[0]));
        $this->assertTrue($staffB->is($property->staff[1]));
    }

    /** @test */
    public function the_api_provider_and_api_identifier_combination_must_be_unique_()
    {
        $this->createTenant();

        // Create a mindbody and booker property with the same api_identifier works.
        $mindbodyPropertyA = factory(Property::class)->create(['api_provider' => 'mindbody', 'api_identifier' => 123]);
        $bookerPropertyA = factory(Property::class)->create(['api_provider' => 'booker', 'api_identifier' => 123]);

        // Try to add another mindbody property with the same api_identifier and an exception is thrown.
        $this->expectException(QueryException::class);
        $mindbodyPropertyB = factory(Property::class)->create(['api_provider' => 'mindbody', 'api_identifier' => 123]);
    }

    /** @test */
    public function it_can_tell_if_the_api_connection_has_been_verified()
    {
        $this->createTenant();

        $verified = factory(Property::class)->create(['api_identifier' => 1, 'verified_at' => now()]);
        $notVerified = factory(Property::class)->create(['api_identifier' => 2, 'verified_at' => null]);

        $this->assertTrue($verified->verified);
        $this->assertFalse($notVerified->verified);
    }

    /** @test */
    public function it_can_be_scoped_by_using_api_identifier()
    {
        $this->createTenant();

        $propertyToFind = factory(Property::class)->create(['api_identifier' => '-99787']);
        $propertyToNotFind = factory(Property::class)->create(['api_identifier' => '12345']);

        $actual = Property::findByApiIdentifier('-99787')->get();

        $this->assertCount(1, $actual);
        $this->assertTrue($propertyToFind->is($actual->first()));
    }

    /** @test */
    public function can_be_scoped_to_verified_properties()
    {
        $this->createTenant();

        $verifiedProperty = factory(Property::class)->create(['api_identifier' => 123]);
        $nonVerifiedProperty = factory(Property::class)->state('not-verified')->create(['api_identifier' => 456]);

        $actual = Property::verified()->get();

        $this->assertCount(1, $actual);
        $this->assertTrue($verifiedProperty->is($actual->first()));
    }

    /** @test */
    public function it_can_set_and_get_session_types_as_meta_property_also_it_does_not_overwrite_existing_meta_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create([ 'api_identifier' => 1, 'meta'  => [ 'verified' => true]]);

        $property->putMeta('session_types', [
            5 => "Office Visit",
            6 => "Outpatient Visit New",
            7 => "add on",
            8 => "Yoga",
            9 => "Advanced Yoga",
            10 => "Initial Consultation (60mins)",
            11 => "Standard Consultation (30mins)",
        ],
        );
        $this->assertTrue($property->verified);

        $this->assertSame([
            5 => "Office Visit",
            6 => "Outpatient Visit New",
            7 => "add on",
            8 => "Yoga",
            9 => "Advanced Yoga",
            10 => "Initial Consultation (60mins)",
            11 => "Standard Consultation (30mins)",
        ], $property->session_types);
    }

    /** @test */
    public function it_can_get_session_types_by_id()
    {
        $this->createTenant();

        $property = factory(Property::class)->create([ 'api_identifier' => -99787, 'meta'  => [ 'verified' => true]]);

        $property->putMeta('session_types', [
            5 => "Office Visit",
            6 => "outpatient visit new",
            7 => "add on",
            8 => "Yoga",
            9 => "Advanced Yoga",
            10 => "Initial Consultation (60mins)",
            11 => "Standard Consultation (30mins)",
        ]);

        $this->assertEquals("Office Visit", $property->sessionTypeById(5));
        $this->assertEquals("outpatient visit new", $property->sessionTypeById(6));
        $this->assertEquals("add on", $property->sessionTypeById(7));
        $this->assertEquals("Yoga", $property->sessionTypeById(8));
        $this->assertEquals("Session Type Id Not Found", $property->sessionTypeById(100));
    }

    /** @test */
    public function can_scope_to_visible_properties_for_the_given_user()
    {
        $this->createTenant();

        $propertyA = factory(Property::class)->create(['api_identifier' => -99787]);
        $propertyB = factory(Property::class)->create(['api_identifier' => 16134]);

        $user = factory(User::class)->state('super-admin')->create();
        $staffWithPermissions = factory(Staff::class)->create(['property_id' => $propertyA->id]);
        $staffWithPermissions->givePermissionTo('properties:view-own');

        $staffWithoutPermissions = factory(Staff::class)->create(['property_id' => $propertyA->id]);

        // The user should be able to see both properties
        $userVisible = Property::visibleToUser($user)->get();
        $this->assertCount(2, $userVisible);

        // The staff member with permissions should be able to see propertyA
        $staffVisible = Property::visibleToUser($staffWithPermissions)->get();
        $this->assertCount(1, $staffVisible);

        // The staff member without permissions should be able to see nothing.
        $noPermissionStaffVisible = Property::visibleToUser($staffWithoutPermissions)->get();
        $this->assertCount(0, $noPermissionStaffVisible);
    }

    /** @test */
    public function the_api_identifier_field_cannot_be_changed_on_verified_properties()
    {
        $this->createTenant();

        Carbon::setTestNow(now()->midDay()); // Set to midDay to avoid micro-time false positives.

        $property = factory(Property::class)->create(['api_identifier' => 'foo', 'verified_at' => now()]);

        $this->assertEquals(now(), $property->verified_at);

        $property->update(['api_identifier' => 'bar']);

        $this->assertEquals(now(), $property->verified_at);
    }
}
