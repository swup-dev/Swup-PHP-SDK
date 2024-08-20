# Swup Documentation

The `Swup` class allows interaction with the Swup API for managing currencies, balances, exchanges, and creating invoices. This class supports asynchronous requests to the API using `GET` and `POST` HTTP methods.

Documentation: [Postman](https://www.postman.com/swup-ai/workspace/swup/documentation/24821794-5c3fe268-1859-4608-a837-45df894ea620)

## Constructor

```php
public function __construct(string $publicKey, string $privateKey, string $locale = 'en')
```
Parameters:

- `publicKey` (string): Your account's public key.
- `privateKey` (string): Your account's private key.
- `locale` (string, optional): Localization for the Accept-Language header. Default is 'en'.

## Usage Example:
```php
$client = new Swup('your_public_key', 'your_private_key');

// Get currencies
$currencies = $client->currencies();

// Create an invoice
$invoice = $client->createInvoice([
    // invoice data
]);

// Fetch an invoice by ID
$invoice = $client->getInvoiceById('id');
```

## Error Handling
The class throws RuntimeException if an HTTP request fails.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled