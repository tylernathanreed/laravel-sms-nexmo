# Laravel SMS Nexmo

[![Latest Stable Version](https://img.shields.io/packagist/v/reedware/laravel-sms-nexmo?label=stable)](https://packagist.org/packages/reedware/laravel-sms-nexmo)
[![Build Status](https://travis-ci.com/tylernathanreed/laravel-sms-nexmo.svg?branch=master)](https://travis-ci.com/tylernathanreed/laravel-sms-nexmo)

## Introduction

This package SMS integrates with Twilio using the [Laravel SMS](https://github.com/tylernathanreed/laravel-sms) package.

## Installation

You may install this package using composer:

    composer require reedware/laravel-sms-nexmo

If you haven't already, you should then follow the [Installation](https://github.com/tylernathanreed/laravel-sms#installation) guide for Laravel SMS.

Set the `default` option in your `config/sms.php` configuration file to `nexmo` (or leverage an environment variable). Next, verify that your nexmo provider configuration file contains the following options:

    'your-driver-name' => [
        'transport' => 'nexmo',
        'api_key' => 'your-nexmo-api-key',
        'api_secret' => 'your-nexmo-secret',
        'signature_secret' => 'your-nexmo-signature-secret',
        
    ],

Once the sms provider has been configured, you can then start sending text messages using Nexmo. To get started and view examples, refer to the [Laravel SMS](https://github.com/tylernathanreed/laravel-sms) documentation.
