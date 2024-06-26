<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Integration\Billing\Traits\TestsCreditCards;
use Tests\Integration\Billing\Traits\TestsCustomers;
use Tests\Integration\Billing\Traits\TestsPaymentMethods;
use Tests\Integration\Billing\Traits\TestsSubscriptions;

trait ContractTests
{
    use RefreshDatabase,
        TestsCustomers,
        TestsPaymentMethods,
        TestsCreditCards,
        TestsSubscriptions;
}
