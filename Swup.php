<?php

class Swup
{
    private string $baseUrl = 'https://api.swup.ai/';

    private string $privateKey;
    private string $publicKey;
    private string $locale;

    public function __construct(
        string $publicKey,
        string $privateKey,
        string $locale = 'en'
    ) {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->locale = $locale;
    }

    public function currencies(): array
    {
        return $this->get('merchant/currencies');
    }

    public function balances(): array
    {
        return $this->get('merchant/balances');
    }

    public function exchangeRates(): array
    {
        return $this->get('merchant/exchange-rates');
    }

    public function getWithdrawalById(string $id): array
    {
        return $this->get('merchant/withdrawals/'.$id);
    }

    public function getWithdrawalByExternalId(string $externalId): array
    {
        return $this->get('merchant/withdrawals/find-one', ['externalId' => $externalId]);
    }

    public function createCryptoWithdrawal(array $data): array
    {
        return $this->post('merchant/withdrawals', $data);
    }

    public function createFiatWithdrawal(array $data): array
    {
        return $this->post('merchant/withdrawals/fiat', $data);
    }

    public function createExchange(array $data): array
    {
        return $this->post('merchant/exchange', $data);
    }

    public function getExchangeByExternalId(string $externalId): array
    {
        return $this->get('merchant/exchanges/find-one', ['externalId' => $externalId]);
    }

    public function getExchangeById(string $id): array
    {
        return $this->get('merchant/exchanges/'.$id);
    }

    public function getExchangeEstimatedAmount(array $params): array
    {
        return $this->get('merchant/exchanges/estimated-amount', $params);
    }

    public function getExchangeMinAmount(array $params): array
    {
        return $this->get('merchant/exchanges/min-amount', $params);
    }

    public function createInvoice(array $data): array
    {
        return $this->post('merchant/invoices', $data);
    }

    public function getInvoiceById(string $id): array
    {
        return $this->get('merchant/invoices/'.$id);
    }

    private function post(string $endpoint, array $data): array
    {
        $ch = curl_init($this->baseUrl.$endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (curl_errno($ch)) {
            throw new RuntimeException(sprintf('http request failed: %s', curl_error($ch)));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function get(string $endpoint, array $params = []): array
    {
        $ch = curl_init($this->baseUrl.$endpoint.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (curl_errno($ch)) {
            throw new RuntimeException(sprintf('http request failed: %s', curl_error($ch)));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function headers(array $data = []): array
    {
        $nonce = base64_encode(random_bytes(10)).time();

        return [
            'x-nonce: '.$nonce,
            'accept: application/json',
            'content-type: application/json',
            'x-merchant-id: '.$this->publicKey,
            'Accept-Language: '.$this->locale,
            'x-merchant-signature: '.$this->createSign($nonce, $data),
        ];
    }

    private function createSign(string $nonce, array $payload): string
    {
        return hash_hmac('sha256', $nonce.json_encode($payload), $this->privateKey);
    }
}
