<?php

use Epoint\Responses\CardRegistrationWithPayResponse;

it('parses successful response correctly', function () {
    $data = [
        'status' => 'success',
        'redirect_url' => 'https://epoint.az/payment/12345',
        'order_id' => 'ORDER-123',
        'transaction' => 'TXN-456',
    ];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccessful())->toBeTrue()
        ->and($response->getStatus())->toBe('success')
        ->and($response->getRedirectUrl())->toBe('https://epoint.az/payment/12345')
        ->and($response->getOrderId())->toBe('ORDER-123')
        ->and($response->getTransactionId())->toBe('TXN-456')
        ->and($response->getMessage())->toBeNull();
});

it('parses error response correctly', function () {
    $data = [
        'status' => 'error',
        'message' => 'Invalid card details',
    ];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccessful())->toBeFalse()
        ->and($response->getStatus())->toBe('error')
        ->and($response->getMessage())->toBe('Invalid card details')
        ->and($response->getRedirectUrl())->toBeNull()
        ->and($response->getOrderId())->toBeNull()
        ->and($response->getTransactionId())->toBeNull();
});

it('handles missing fields gracefully', function () {
    $data = [];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccessful())->toBeFalse()
        ->and($response->getStatus())->toBe('error')
        ->and($response->getRedirectUrl())->toBeNull()
        ->and($response->getOrderId())->toBeNull()
        ->and($response->getTransactionId())->toBeNull()
        ->and($response->getMessage())->toBeNull();
});