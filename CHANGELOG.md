# Changelog

All notable changes to `rafoabbas/epoint-php` will be documented in this file.

## 0.2.2 - 2026-06-19

### Added
- Card registration status check (`checkCardStatus()`) - Check card status using card ID via `/get-status-card` endpoint
- `CardStatusCheckRequest` class for building card status check requests
- `CardStatusResponse` class with getter methods: `getCardStatus()`, `getCardId()`, `getCardName()`, `getCardMask()`, `getExpiredDate()`, `getDescription()`
- `CardStatus` enum with values: `NEW`, `ACTIVE`, `PENDING`, `REJECTED`, `EXPIRED`, `SESSION_EXPIRED`

### Documentation
- Updated Payment Status Check wiki with card registration status section
- Updated API Reference with `checkCardStatus()` method and `CardStatusResponse`
- Updated Response Objects wiki with Card Status Response documentation
- Updated README with card status check example and `CardStatus` enum

## 0.2.1 - 2026-02-25

### Added
- `getCardId()` method to `StatusResponse` class for retrieving card ID from payment status responses

## 0.2.0 - 2026-02-24

### Added
- Card Registration with Payment (`registerCardWithPay()`) - Register card and process payment in one step
- Split Card Payment (`splitCardPayment()`) - Execute split payments with saved cards
- Split Card Registration with Payment (`splitCardRegistrationWithPay()`) - Register card with split payment
- New response methods across all response classes:
  - `getBankResponse()` - Get bank response code
  - `getOperationCode()` - Get operation code
  - `getRrn()` - Get retrieval reference number
  - `getOtherAttributes()` - Get additional attributes array
- `CardRegistrationWithPayResponse` class with comprehensive getter methods
- `currency()` method for `SavedCardPaymentRequest`
- `currency()`, `language()`, `otherAttributes()` methods for `PreauthRequest`
- `currency()`, `language()`, `otherAttributes()` methods for `SplitPaymentRequest`

### Enhanced
- Updated all response classes to include bank response fields
- Improved `PreauthCompleteResponse` with card details and bank transaction info
- Enhanced API documentation with new methods and parameters

### Documentation
- Added comprehensive wiki documentation for all new features
- Updated Card Management guide with card-registration-with-pay examples
- Added Split Payments section to Standard Payments guide
- Updated API Reference with new methods and response objects
- Enhanced Response Objects documentation

## 0.1.0 - 2026-02-24

### Added
- Initial release
- Standard payment requests
- Card registration and saved card payments
- Split payments
- Preauth (hold and capture)
- Refunds and reversals
- Apple Pay & Google Pay widget integration
- Wallet operations
- Invoice management
- Signature verification for callbacks
- Comprehensive test coverage
- Full API documentation