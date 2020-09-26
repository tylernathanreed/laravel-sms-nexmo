<?php

namespace Reedware\LaravelSMS\Nexmo;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Reedware\LaravelSMS\Events\ManagerBooted;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\Client\Credentials\Container;
use Vonage\Client\Credentials\Keypair;
use Vonage\Client\Credentials\SignatureSecret;

class NexmoServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(ManagerBooted::class, function($event) {
            $event->manager->extend('nexmo', function($app, $name, $config) {
                return $this->createNexmoTransport($name, $config);
            });
        });
    }

    /**
     * Creates and returns the nexmo transport implementation.
     *
     * @param  string  $name
     * @param  array   $config
     *
     * @return \Reedware\LaravelSMS\Nexmo\NexmoTransport
     *
     * @throws \InvalidArgumentException
     */
    protected function createNexmoTransport($name, $config)
    {
        // Ensure that an application id is present when a private key is provided
        if(isset($config['private']) && !isset($config['app_id'])) {
            throw new InvalidArgumentException("Unable to create provider [{$name}]: An application id is required when using a private key.");
        }

        // Ensure that basic and signature credentials are not provided together
        if(isset($config['secret']) && isset($config['signature'])) {
            throw new InvalidArgumentException("Unable to create provider [{$name}]: Cannot use both secret and signature.");
        }

        // Create the credentials
        $credentials = $this->createNexmoCredentials($config);

        // Ensure that credentials were provided
        if(is_null($credentials)) {
            throw new InvalidArgumentException("Unable to create provider [{$name}]: Insufficient credentials provided.");
        }

        // Determine the client options
        $options = array_diff_key($config, [
            'app',
            'app_id',
            'key',
            'private',
            'secret',
            'signature'
        ]);

        // Determine the http client
        $httpClient = isset($config['http_client'])
            ? $this->app->make($config['http_client'])
            : null;

        // Create and return the nexmo transport
        return new NexmoTransport(
            new Client(
                $credentials,
                $options,
                $httpClient
            )
        );
    }

    /**
     * Creates the specified nexmo credentials.
     *
     * @param  array  $config
     *
     * @return \Vonage\Client\Credentials\CredentialsInterface|null
     */
    protected function createNexmoCredentials($config)
    {
        // Check for basic credentials
        if(isset($config['secret'])) {
            $basic = new Basic($config['key'], $config['secret']);
        }

        // Check for signature credentials
        if(isset($config['signature'])) {
            $signature = new SignatureSecret($config['key'], $config['signature']);
        }

        // Check for private key credentials
        if(isset($config['private'])) {
            $private = new Keypair($this->loadPrivateKey($config['private']), $config['app_id']);
        }

        // Check for multiple credentials
        if(isset($private) && (isset($basic) || isset($signature))) {
            $container = new Container($private, $basic ?? $signature);
        }

        // Return the credentials
        return $container ?? $private ?? $basic ?? $signature ?? null;
    }

    /**
     * Loads the specified private key contents.
     *
     * @param  string  $key
     *
     * @return string
     */
    protected function loadPrivateKey($key)
    {
        // Use a fake key during unit tests
        if(app()->runningUnitTests()) {
            return '===FAKE KEY===';
        }

        // If the key is the key itself, return it
        if(Str::startsWith($key, '-----BEGIN PRIVATE KEY-----')) {
            return $key;
        }

        // Otherwise, treat the key as a path to the real key
        return file_get_contents($key[0] !== '/' ? $this->app->basePath($key) : $key);
    }
}
