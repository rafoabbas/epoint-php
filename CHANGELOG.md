# Changelog

All notable changes to `rafoabbas/epoint-php` will be documented in this file.

## 0.2.5 - 2026-10-22

### Added
- `StatusCheckRequest::orderId()` - Check payment status by your own order ID (`order_id`) instead of the Epoint transaction ID. Needed for widget (Apple Pay / Google Pay) payments, where `/token/widget` returns no transaction ID. `transaction()` keeps working as before; when both are set, Epoint uses the transaction ID.
- `StatusResponse::getOrderId()` (it was documented but missing)

### Changed
- `checkStatus()->get()` without an identifier now throws `Transaction ID or order ID is required` (was `Transaction ID is required`)

### Documentation
- Updated README, Payment Status Check, API Reference and Response Objects wiki pages

## 0.2.4 - 2026-10-22

### Added
- Active payment methods (`paymentMethods()`) - Get active payment methods, wallets and installment banks via `/payment-methods` endpoint
- `PaymentMethodsRequest` class with optional `language()` (installment card names are returned in this language)
- `PaymentMethodsResponse` class with getter methods: `getBusinessId()`, `getBusinessName()`, `getMethods()`, `hasMethod()`, `getWallets()`, `getInstallment()`, `getInstallmentMinAmount()`, `getInstallmentBanks()`
- `PaymentMethod` enum with values: `CARD`, `INSTALLMENT`, `WALLET`, `GOOGLE_PAY`, `APPLE_PAY`, `CLICK_TO_PAY`, `AMEX`, `PRE_AUTH`, `CARD_SAVE`, `PAYOUT`, `REVERSE`
- Installment payment (`installmentPayment()`) - Create payment with a specific installment bank and month via `/installment-request` endpoint
- `InstallmentPaymentRequest` class with `installmentCardId()` and `installmentMonth()` methods

### Documentation
- Updated README, API Reference, Response Objects and Standard Payments wiki pages
- Added `examples/payment-methods.php`

## 0.2.3 - 2026-06-19

### Fixed
- `CardStatusResponse::isSuccess()` now checks for `active` card status

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