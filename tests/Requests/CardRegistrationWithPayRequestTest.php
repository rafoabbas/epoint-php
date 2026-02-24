<?php

use Epoint\Enums\Currency;
use Epoint\Enums\Language;
use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;

beforeEach(function () {
    $this->client = new EpointClient('test_public_key', 'test_private_key');
});

it('can create card registration with pay request', function () {
    $request = $this->client->registerCardWithPay();

    expect($request)->toBeInstanceOf(\Epoint\Requests\CardRegistrationWithPayRequest::class);
});

it('builds request data correctly', function () {
    $request = $this->client->registerCardWithPay()
        ->amount(100.50)
        ->orderId('TEST-123')
        ->description('Test card registration with payment')
        ->currency(Currency::USD)
        ->language(Language::EN)
        ->successUrl('https://example.com/success')
        ->errorUrl('https://example.com/error');

    $reflection = new ReflectionClass($request);
    $property = $reflection->getProperty('data');
    $property->setAccessible(true);
    $data = $property->getValue($request);

    expect($data)->toMatchArray([
        'public_key' => 'test_public_key',
        'amount' => 100.50,
        'order_id' => 'TEST-123',
        'description' => 'Test card registration with payment',
        'currency' => 'USD',
        'language' => 'en',
        'refund' => 0,
        'success_redirect_url' => 'https://example.com/success',
        'error_redirect_url' => 'https://example.com/error',
    ]);
});

it('sets refund flag correctly', function () {
    $request = $this->client->registerCardWithPay()
        ->amount(50.00)
        ->orderId('TEST-456')
        ->forRefund();

    $reflection = new ReflectionClass($request);
    $property = $reflection->getProperty('data');
    $property->setAccessible(true);
    $data = $property->getValue($request);

    expect($data['refund'])->toBe(1);
});

it('throws exception when amount is missing', function () {
    $this->client->registerCardWithPay()
        ->orderId('TEST-789')
        ->send();
})->throws(EpointException::class, 'Missing required field: amount');

it('throws exception when order_id is missing', function () {
    $this->client->registerCardWithPay()
        ->amount(75.00)
        ->send();
})->throws(EpointException::class, 'Missing required field: order_id');