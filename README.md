# masterhosteg Laravel SDK

**Laravel SDK Author:** YonkoSam

A robust Laravel SDK for interacting with the masterhosteg ([https://masterhosteg.com](https://masterhosteg.com)). This SDK simplifies sending WhatsApp messages, managing contacts and groups, handling session statuses, and processing incoming webhooks with Laravel-native events and best practices.

---

## Features

- **Modern Laravel Service:** Injectable, testable, and supports Facade usage.
- **Message Sending:**
  - Helper methods for text, image, video, document, audio, sticker, contact, and location messages.
  - DTOs for type-safe payloads.
  - RetryConfig for automatic rate-limit retries on send-message endpoints.
- **Contact Management:** List, get info, get profile picture, block, and unblock contacts.
- **Group Management:** List groups, get metadata, manage participants, update settings.
- **Session Management:** Create, list, update, delete, connect/disconnect sessions, get QR codes, check status.
- **Webhook Handling:** Securely verifies and parses incoming webhooks, dispatches Laravel events for each event type.
- **Error Handling:** Custom `masterhostegException` with detailed error/response info.
- **Testing:** Pest and Testbench support out of the box.

---

## Installation

```bash
composer require MasterSoftEG/masterhostwpapi-laravel
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=masterhosteg-config
```

Set your credentials in `.env`:

```
masterhosteg_API_KEY=your_api_key_here
masterhosteg_PERSONAL_ACCESS_TOKEN=your_personal_token_here # (optional, for session management)
masterhosteg_WEBHOOK_SECRET=your_webhook_secret_here # (for webhook verification)
```

---

## Usage

### Dependency Injection (Recommended)

```php
use masterhosteg\masterhostClient;

public function send(masterhostClient $client) {
    $client->sendText('1234567890', 'Hello from Laravel!');
}
```



### Facade Usage

```php
use masterhosteg\Facades\masterhosteg;

masterhosteg::sendText('1234567890', 'Hello via Facade!');
```


#### Direct Instantiation

Another way to use the client is by creating a new instance of the `masterhostClient` class directly and providing your API key as an argument:

```php
$client = new masterhostClient('your_api_key_here');
```

This method is particularly powerful and becomes the recommended approach when your application needs to manage multiple sessions.
Each session you create in your masterhost dashboard has a unique API key. Direct instantiation allows you to dynamically select and use the specific API key for the session you want to communicate with at runtime. This is essential for applications that send messages from different WhatsApp numbers.
This approach is also the standard method for using the library in contexts outside of the Laravel framework or in situations where Laravel's service container is not readily available. To get started, you'll need a masterhosteg account to obtain the unique API key for each of your sessions.

### DTO Usage

```php
use masterhosteg\Data\SendTextMessageData;
use masterhosteg\Data\RetryConfig;
use masterhosteg\Facades\masterhosteg;

$dto = new SendTextMessageData('1234567890', 'Hello DTO!');
$retry = new RetryConfig(true, 3); // Enable retry, max 3 times
masterhosteg::sendText($dto, null, [], $retry);
```

---

## Message Sending

All message types support both direct parameters and DTOs. All return an array response.

- `sendText($to, $text, $options = [], ?RetryConfig $retry = null)`
- `sendImage($to, $url, $caption = null, $options = [], ?RetryConfig $retry = null)`
- `sendVideo($to, $url, $caption = null, $options = [], ?RetryConfig $retry = null)`
- `sendDocument($to, $url, $filename, $caption = null, $options = [], ?RetryConfig $retry = null)`
- `sendAudio($to, $url, $options = [], ?RetryConfig $retry = null)`
- `sendSticker($to, $url, $options = [], ?RetryConfig $retry = null)`
- `sendContact($to, $contactName, $contactPhone, $options = [], ?RetryConfig $retry = null)`
- `sendLocation($to, $latitude, $longitude, $name = null, $address = null, $options = [], ?RetryConfig $retry = null)`

**RetryConfig:** Only applies to send-message endpoints. Retries on HTTP 429 (rate limit) up to `maxRetries` times, waiting for `retry_after` seconds if provided.

---

## Contact Management

- `getContacts()`
- `getContactInfo($phone)`
- `getContactProfilePicture($phone)`
- `blockContact($phone)`
- `unblockContact($phone)`

---

## Group Management

- `getGroups()`
- `getGroupMetadata($jid)`
- `getGroupParticipants($jid)`
- `addGroupParticipants($jid, $participants)`
- `removeGroupParticipants($jid, $participants)`
- `updateGroupSettings($jid, $settings)`

---

## Session Management

- `getAllWhatsAppSessions()`
- `createWhatsAppSession($payload)`
- `getWhatsAppSessionDetails($sessionId)`
- `updateWhatsAppSession($sessionId, $payload)`
- `deleteWhatsAppSession($sessionId)`
- `connectWhatsAppSession($sessionId, $qrAsImage = false)`
- `getWhatsAppSessionQrCode($sessionId)`
- `disconnectWhatsAppSession($sessionId)`
- `regenerateApiKey($sessionId)`
- `getSessionStatus($sessionId)`

---

## Webhook Handling & Events

- The package auto-registers a POST route (default `/masterhost/webhook`).
- Verifies the signature using the secret in config/env.
- Dispatches a dedicated Laravel event for each webhook event type (e.g., `MessagesUpserted`, `GroupsUpdated`, etc.).
- Listen for events in your app:

```php
use masterhosteg\Events\MessagesUpserted;

Event::listen(MessagesUpserted::class, function ($event) {
    // $event->payload
});
```

---

## Error Handling

All API errors throw `masterhostegException`.

```php
use masterhosteg\Exceptions\masterhostegException;

try {
    masterhosteg::sendText('123', 'fail');
} catch (masterhostegException $e) {
    // $e->getMessage(), $e->getCode(), $e->getResponse()
}
```

---

## DTO Reference

- `SendTextMessageData($to, $text)`
- `SendImageMessageData($to, $imageUrl, $text = null)`
- `SendVideoMessageData($to, $videoUrl, $text = null)`
- `SendDocumentMessageData($to, $documentUrl, $filename, $text = null)`
- `SendAudioMessageData($to, $audioUrl)`
- `SendStickerMessageData($to, $stickerUrl)`
- `SendContactMessageData($to, $contactName, $contactPhone)`
- `SendLocationMessageData($to, $latitude, $longitude, $name = null, $address = null)`
- `RetryConfig($enabled = false, $maxRetries = 0)`

---

## Testing

- Pest is included for expressive, modern tests.
- Example:

```php
test('sendText via Facade returns success and message', function () {
    Http::fake([
        'https://masterhosteg.com/api/send-message' => Http::response(['success' => true, 'message' => 'Message sent'], 200),
    ]);
    $result = masterhosteg::sendText('123', 'hello');
    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('Message sent');
});
```

---

## Advanced

- **Customizing HTTP:** Uses Laravel's HTTP client. You can mock or extend as needed.
- **Extending Events:** You can add listeners for any event type or extend event classes for custom logic.
- **RetryConfig:** Only applies to send-message endpoints. Retries on HTTP 429 (rate limit) up to `maxRetries` times, waiting for `retry_after` seconds if provided.

---

## License

MIT 
