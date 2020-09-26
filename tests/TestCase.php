<?php

namespace Reedware\LaravelSMS\Tests\Nexmo;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Returns the package providers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Reedware\LaravelSMS\SMSServiceProvider::class,
            \Reedware\LaravelSMS\Nexmo\NexmoServiceProvider::class
        ];
    }
}