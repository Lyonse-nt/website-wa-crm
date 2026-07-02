# 📡 API Reference - Mini WhatsApp CRM

Dokumentasi lengkap WhatsApp Cloud API yang digunakan dalam project ini.

---

## 🔑 Authentication

Semua request ke WhatsApp Cloud API memerlukan access token.

**Header:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Base URL:**
```
https://graph.facebook.com/v18.0/{PHONE_NUMBER_ID}
```

---

## 📤 Send Messages

### 1. Send Text Message

**Endpoint:**
```
POST /messages
```

**Request Body:**
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "628123456789",
  "type": "text",
  "text": {
    "preview_url": false,
    "body": "Hello from WhatsApp API!"
  }
}
```

**Response Success:**
```json
{
  "messaging_product": "whatsapp",
  "contacts": [
    {
      "input": "628123456789",
      "wa_id": "628123456789"
    }
  ],
  "messages": [
    {
      "id": "wamid.HBgLNjI4MTIzNDU2Nzg5FQIAERgSQTdBRjE2QjVEMjBCMjY5OTQA"
    }
  ]
}
```

**Response Error:**
```json
{
  "error": {
    "message": "Invalid OAuth access token",
    "type": "OAuthException",
    "code": 190,
    "fbtrace_id": "AXxxxx"
  }
}
```

---

### 2. Send Image

**Request Body:**
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "628123456789",
  "type": "image",
  "image": {
    "link": "https://example.com/image.jpg",
    "caption": "Check out this image!"
  }
}
```

**Alternative (Media ID):**
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "628123456789",
  "type": "image",
  "image": {
    "id": "MEDIA_ID"
  }
}
```

---

### 3. Send Document

**Request Body:**
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "628123456789",
  "type": "document",
  "document": {
    "link": "https://example.com/document.pdf",
    "filename": "Invoice.pdf",
    "caption": "Here's your invoice"
  }
}
```

---

### 4. Send Template Message

**Request Body:**
```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "628123456789",
  "type": "template",
  "template": {
    "name": "hello_world",
    "language": {
      "code": "en_US"
    }
  }
}
```

**With Parameters:**
```json
{
  "messaging_product": "whatsapp",
  "to": "628123456789",
  "type": "template",
  "template": {
    "name": "sample_invoice",
    "language": {
      "code": "id"
    },
    "components": [
      {
        "type": "body",
        "parameters": [
          {
            "type": "text",
            "text": "John Doe"
          },
          {
            "type": "text",
            "text": "Rp 500.000"
          }
        ]
      }
    ]
  }
}
```

---

## 📥 Webhook Events

### Webhook Verification (GET)

**Request:**
```
GET /webhook?hub.mode=subscribe&hub.verify_token=YOUR_VERIFY_TOKEN&hub.challenge=CHALLENGE_STRING
```

**Response:**
```
CHALLENGE_STRING
```

---

### Receive Message (POST)

**Webhook Payload:**
```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "WHATSAPP_BUSINESS_ACCOUNT_ID",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "6281234567890",
              "phone_number_id": "PHONE_NUMBER_ID"
            },
            "contacts": [
              {
                "profile": {
                  "name": "John Doe"
                },
                "wa_id": "628123456789"
              }
            ],
            "messages": [
              {
                "from": "628123456789",
                "id": "wamid.HBgLNjI4MTIzNDU2Nzg5FQIAERgSQTdBRjE2QjVEMjBCMjY5OTQA",
                "timestamp": "1703001234",
                "text": {
                  "body": "Hello"
                },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

---

### Message Status Update

**Payload:**
```json
{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "WHATSAPP_BUSINESS_ACCOUNT_ID",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "6281234567890",
              "phone_number_id": "PHONE_NUMBER_ID"
            },
            "statuses": [
              {
                "id": "wamid.HBgLNjI4MTIzNDU2Nzg5FQIAERgSQTdBRjE2QjVEMjBCMjY5OTQA",
                "status": "delivered",
                "timestamp": "1703001234",
                "recipient_id": "628123456789"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}
```

**Status Values:**
- `sent` - Message sent to WhatsApp servers
- `delivered` - Message delivered to recipient
- `read` - Message read by recipient
- `failed` - Message failed to send

---

## 🖼️ Media Management

### Upload Media

**Endpoint:**
```
POST /{PHONE_NUMBER_ID}/media
```

**Headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: multipart/form-data
```

**Form Data:**
```
file: [binary]
type: image/jpeg
messaging_product: whatsapp
```

**Response:**
```json
{
  "id": "MEDIA_ID"
}
```

---

### Get Media URL

**Endpoint:**
```
GET /{MEDIA_ID}
```

**Response:**
```json
{
  "url": "https://lookaside.fbsbx.com/whatsapp_business/attachments/?mid=xxx",
  "mime_type": "image/jpeg",
  "sha256": "xxx",
  "file_size": 123456,
  "id": "MEDIA_ID",
  "messaging_product": "whatsapp"
}
```

---

### Download Media

**Endpoint:**
```
GET {MEDIA_URL}
```

**Headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Response:**
Binary file content

---

## 📋 Message Types

### Supported Types

| Type | Description | Max Size |
|------|-------------|----------|
| `text` | Text message | 4096 chars |
| `image` | JPEG, PNG | 5 MB |
| `document` | PDF, DOC, etc | 100 MB |
| `audio` | MP3, OGG, AMR | 16 MB |
| `video` | MP4, 3GP | 16 MB |
| `sticker` | WEBP | 100 KB |
| `location` | GPS coordinates | - |
| `contacts` | vCard | - |
| `template` | Pre-approved template | - |

---

## 🎯 Interactive Messages

### Button Message

```json
{
  "messaging_product": "whatsapp",
  "to": "628123456789",
  "type": "interactive",
  "interactive": {
    "type": "button",
    "body": {
      "text": "Choose an option:"
    },
    "action": {
      "buttons": [
        {
          "type": "reply",
          "reply": {
            "id": "btn_1",
            "title": "Option 1"
          }
        },
        {
          "type": "reply",
          "reply": {
            "id": "btn_2",
            "title": "Option 2"
          }
        }
      ]
    }
  }
}
```

---

### List Message

```json
{
  "messaging_product": "whatsapp",
  "to": "628123456789",
  "type": "interactive",
  "interactive": {
    "type": "list",
    "header": {
      "type": "text",
      "text": "Choose a category"
    },
    "body": {
      "text": "Select one option"
    },
    "footer": {
      "text": "Footer text"
    },
    "action": {
      "button": "View Options",
      "sections": [
        {
          "title": "Section 1",
          "rows": [
            {
              "id": "opt_1",
              "title": "Option 1",
              "description": "Description 1"
            },
            {
              "id": "opt_2",
              "title": "Option 2",
              "description": "Description 2"
            }
          ]
        }
      ]
    }
  }
}
```

---

## ⚠️ Error Codes

| Code | Type | Description |
|------|------|-------------|
| 190 | OAuthException | Invalid access token |
| 131030 | Recipient not available | Number not on WhatsApp |
| 131031 | Rate limit hit | Too many messages |
| 131047 | Re-engagement message | 24hr window expired |
| 131051 | Unsupported message type | Type not supported |
| 132000 | Template not found | Invalid template name |
| 132001 | Parameter error | Wrong parameters |
| 133000 | Generic error | Check error message |

---

## 🔄 Rate Limits

### Cloud API Limits

| Tier | Messages/Day | Concurrent Requests |
|------|--------------|---------------------|
| Development | 250 | 20 |
| Tier 1 | 1,000 | 60 |
| Tier 2 | 10,000 | 80 |
| Tier 3 | 100,000 | 100 |
| Unlimited | Unlimited | 200 |

**Note:** Limits increase automatically based on quality rating and phone number status.

---

## 📞 Phone Number Formats

**Supported Formats:**
- International: `628123456789` ✅
- With plus: `+628123456789` ✅
- Local: `08123456789` ❌ (needs conversion)

**Conversion Function:**
```php
function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (substr($phone, 0, 1) === '0') {
        return '62' . substr($phone, 1);
    }
    
    if (substr($phone, 0, 2) !== '62') {
        return '62' . $phone;
    }
    
    return $phone;
}
```

---

## 🔐 Webhook Security

### Verify Signature

WhatsApp signs all webhook requests. Verify using:

```php
function verifyWebhook($payload, $signature) {
    $appSecret = env('WHATSAPP_APP_SECRET');
    $expectedSignature = hash_hmac('sha256', $payload, $appSecret);
    
    return hash_equals($signature, 'sha256=' . $expectedSignature);
}
```

**Usage:**
```php
$signature = $request->header('X-Hub-Signature-256');
$payload = $request->getContent();

if (!verifyWebhook($payload, $signature)) {
    abort(403, 'Invalid signature');
}
```

---

## 🎓 Best Practices

### 1. Message Templates

- Use templates for first contact (outside 24hr window)
- Get templates approved before use
- Keep templates simple and clear

### 2. Response Time

- Respond within 24 hours to keep conversation open
- After 24hrs, need template message

### 3. Error Handling

```php
try {
    $result = $whatsappService->sendText($to, $message);
    
    if (!$result['success']) {
        Log::error('WhatsApp send failed', $result['data']);
        // Retry logic or alert
    }
} catch (\Exception $e) {
    Log::error('WhatsApp exception', ['error' => $e->getMessage()]);
    // Handle exception
}
```

### 4. Rate Limiting

- Implement exponential backoff
- Queue messages for bulk sending
- Monitor daily limits

### 5. Logging

- Log all API requests/responses
- Log webhook payloads
- Monitor error patterns

---

## 📚 Additional Resources

**Official Docs:**
- [Cloud API Overview](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [Getting Started](https://developers.facebook.com/docs/whatsapp/cloud-api/get-started)
- [Message Templates](https://developers.facebook.com/docs/whatsapp/message-templates)
- [Webhooks](https://developers.facebook.com/docs/whatsapp/cloud-api/webhooks)

**Tools:**
- [API Explorer](https://developers.facebook.com/tools/explorer)
- [Webhook Tester](https://webhook.site)
- [Postman Collection](https://www.postman.com/meta)

---

## 💡 Tips

1. **Test Mode**: Use test numbers during development
2. **Sandbox**: Meta provides sandbox for testing
3. **Templates**: Pre-approve templates early
4. **Media**: Host media on reliable CDN
5. **Monitoring**: Set up alerts for errors
6. **Backup**: Store message history securely

---

**Need Help?**
- Check official documentation
- Visit Meta Business Help Center
- Community forums
- Stack Overflow

---

Updated: 2026-07-01
