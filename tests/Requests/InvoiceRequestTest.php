<?php

declare(strict_types=1);

namespace Epoint\Tests\Requests;

use Epoint\EpointClient;
use Epoint\Requests\InvoiceRequest;
use Mockery;
use PHPUnit\Framework\TestCase;

class InvoiceRequestTest extends TestCase
{
    private EpointClient $client;

    protected function setUp(): void
    {
        $this->client = new EpointClient(
            publicKey: 'i000000001',
            privateKey: 'test-private-key'
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_can_create_invoice(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/create', Mockery::on(function ($data) {
                return $data['sum'] === 150.00
                    && $data['name'] === 'John Doe'
                    && $data['public_key'] === 'i000000001';
            }))
            ->andReturn([
                'status' => 'success',
                'id' => 123,
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->create([
            'sum' => 150.00,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_can_update_invoice(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/update', Mockery::on(function ($data) {
                return $data['id'] === 123
                    && $data['sum'] === 200.00;
            }))
            ->andReturn([
                'status' => 'success',
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->update(123, ['sum' => 200.00]);

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_can_view_invoice(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/view', Mockery::on(function ($data) {
                return $data['id'] === 123;
            }))
            ->andReturn([
                'status' => 'success',
                'invoice' => ['id' => 123, 'sum' => 150.00],
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->view(123);

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_can_list_invoices(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/list', Mockery::on(function ($data) {
                return $data['public_key'] === 'i000000001';
            }))
            ->andReturn([
                'status' => 'success',
                'invoices' => [
                    ['id' => 123, 'sum' => 150.00],
                    ['id' => 124, 'sum' => 200.00],
                ],
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->list();

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_can_send_invoice_via_sms(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/send-sms', Mockery::on(function ($data) {
                return $data['id'] === 123
                    && $data['phone'] === '+994501234567';
            }))
            ->andReturn([
                'status' => 'success',
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->sendSms(123, '+994501234567');

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function test_can_send_invoice_via_email(): void
    {
        $mockClient = Mockery::mock(EpointClient::class);
        $mockClient->shouldReceive('getPublicKey')->andReturn('i000000001');
        $mockClient->shouldReceive('post')
            ->once()
            ->with('/invoices/send-email', Mockery::on(function ($data) {
                return $data['id'] === 123
                    && $data['email'] === 'john@example.com';
            }))
            ->andReturn([
                'status' => 'success',
            ]);

        $request = new InvoiceRequest($mockClient);
        $response = $request->sendEmail(123, 'john@example.com');

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }
}