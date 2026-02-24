<?php

use Epoint\Responses\CardRegistrationWithPayResponse;

it('parses successful response correctly', function () {
    $data = [
        'status' => 'success',
        'redirect_url' => 'https://epoint.az/payment/12345',
        'order_id' => 'ORDER-123',
        'transaction' => 'TXN-456',
        'card_id' => 'CARD-789',
        'amount' => 100.50,
    ];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getStatus())->toBe('success')
        ->and($response->getRedirectUrl())->toBe('https://epoint.az/payment/12345')
        ->and($response->getOrderId())->toBe('ORDER-123')
        ->and($response->getTransaction())->toBe('TXN-456')
        ->and($response->getCardId())->toBe('CARD-789')
        ->and($response->getAmount())->toBe(100.50)
        ->and($response->getMessage())->toBeNull();
});

it('parses error response correctly', function () {
    $data = [
        'status' => 'error',
        'message' => 'Invalid card details',
    ];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccess())->toBeFalse()
        ->and($response->isError())->toBeTrue()
        ->and($response->getStatus())->toBe('error')
        ->and($response->getMessage())->toBe('Invalid card details')
        ->and($response->getRedirectUrl())->toBeNull()
        ->and($response->getOrderId())->toBeNull()
        ->and($response->getTransaction())->toBeNull()
        ->and($response->getCardId())->toBeNull();
});

it('handles missing fields gracefully', function () {
    $data = [];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->isSuccess())->toBeFalse()
        ->and($response->getStatus())->toBeNull()
        ->and($response->getRedirectUrl())->toBeNull()
        ->and($response->getOrderId())->toBeNull()
        ->and($response->getTransaction())->toBeNull()
        ->and($response->getCardId())->toBeNull()
        ->and($response->getMessage())->toBeNull();
});

it('provides access to full response data', function () {
    $data = [
        'status' => 'success',
        'card_id' => 'CARD-123',
        'transaction' => 'TXN-456',
        'card_mask' => '123456******1234',
        'card_name' => 'JOHN DOE',
    ];

    $response = new CardRegistrationWithPayResponse($data);

    expect($response->toArray())->toBe($data)
        ->and($response->getCardMask())->toBe('123456******1234')
        ->and($response->getCardName())->toBe('JOHN DOE');
});