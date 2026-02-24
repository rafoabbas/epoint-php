<?php

declare(strict_types=1);

namespace Epoint\Requests;

use Epoint\EpointClient;
use Epoint\Exceptions\EpointException;

class InvoiceRequest
{
    public function __construct(private readonly EpointClient $client)
    {
    }

    /**
     * Create invoice
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function create(array $data): array
    {
        $data['public_key'] = $this->client->getPublicKey();

        return $this->client->post('/invoices/create', $data);
    }

    /**
     * Update invoice
     *
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function update(int $id, array $data): array
    {
        $data['public_key'] = $this->client->getPublicKey();
        $data['id'] = $id;

        return $this->client->post('/invoices/update', $data);
    }

    /**
     * Get invoice details
     *
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function view(int $id): array
    {
        return $this->client->post('/invoices/view', [
            'public_key' => $this->client->getPublicKey(),
            'id' => $id,
        ]);
    }

    /**
     * Get list of invoices
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function list(array $filters = []): array
    {
        $filters['public_key'] = $this->client->getPublicKey();

        return $this->client->post('/invoices/list', $filters);
    }

    /**
     * Send invoice via SMS
     *
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function sendSms(int $id, string $phone): array
    {
        return $this->client->post('/invoices/send-sms', [
            'public_key' => $this->client->getPublicKey(),
            'id' => $id,
            'phone' => $phone,
        ]);
    }

    /**
     * Send invoice via email
     *
     * @return array<string, mixed>
     *
     * @throws EpointException
     */
    public function sendEmail(int $id, string $email): array
    {
        return $this->client->post('/invoices/send-email', [
            'public_key' => $this->client->getPublicKey(),
            'id' => $id,
            'email' => $email,
        ]);
    }
}