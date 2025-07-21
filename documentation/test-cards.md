# Stripe Test Credit Cards Documentation

This document contains all test credit card numbers that can be used for testing the ChordHound donation/payment functionality in development and testing environments.

## Important Notes

- These test cards only work with Stripe test API keys (starting with `sk_test_` and `pk_test_`)
- Never use these card numbers in production
- Use any future expiration date (e.g., 12/34)
- Use any 3-digit CVC for most cards (4-digit for American Express)
- Use any postal code
- These cards simulate successful payments unless otherwise noted

## Test Card Numbers

### Basic International Test Cards

| Brand | Number | CVC | Date | Notes |
|-------|--------|-----|------|-------|
| Visa | 4242424242424242 | Any 3 digits | Any future date | Default test card |
| Visa (debit) | 4000056655665556 | Any 3 digits | Any future date | Debit card |
| Mastercard | 5555555555554444 | Any 3 digits | Any future date | Standard credit |
| Mastercard (2-series) | 2223003122003222 | Any 3 digits | Any future date | 2-series BIN |
| Mastercard (debit) | 5200828282828210 | Any 3 digits | Any future date | Debit card |
| Mastercard (prepaid) | 5105105105105100 | Any 3 digits | Any future date | Prepaid card |
| American Express | 378282246310005 | Any 4 digits | Any future date | Standard Amex |
| American Express | 371449635398431 | Any 4 digits | Any future date | Alternative Amex |
| Discover | 6011111111111117 | Any 3 digits | Any future date | Standard Discover |
| Discover | 6011000990139424 | Any 3 digits | Any future date | Alternative Discover |
| Discover (debit) | 6011981111111113 | Any 3 digits | Any future date | Debit card |
| Diners Club | 3056930009020004 | Any 3 digits | Any future date | Standard Diners |
| Diners Club (14-digit) | 36227206271667 | Any 3 digits | Any future date | 14-digit card |
| BCcard and DinaCard | 6555900000604105 | Any 3 digits | Any future date | Korean cards |
| JCB | 3566002020360505 | Any 3 digits | Any future date | Japanese Credit Bureau |
| UnionPay | 6200000000000005 | Any 3 digits | Any future date | Standard UnionPay |
| UnionPay (debit) | 6200000000000047 | Any 3 digits | Any future date | Debit card |
| UnionPay (19-digit) | 6205500000000000004 | Any 3 digits | Any future date | 19-digit card |

## Usage in Tests

To use these test cards in automated tests:

1. Ensure your `.env.testing` file has valid Stripe test keys:
   ```
   STRIPE_KEY=pk_test_your_stripe_publishable_key_here
   STRIPE_SECRET=sk_test_your_stripe_secret_key_here
   ```

2. The test suite in `tests/Feature/SupportSiteTest.php` includes comprehensive tests for all major card brands.

3. Tests will automatically skip if Stripe test keys are not configured.

## Testing Different Scenarios

While the cards above simulate successful payments, Stripe also provides test cards for specific scenarios:

### Declined Payments
- `4000000000000002` - Card declined (generic decline)
- `4000000000009995` - Card declined (insufficient funds)
- `4000000000009987` - Card declined (lost card)
- `4000000000009979` - Card declined (stolen card)

### 3D Secure Authentication
- `4000000000003220` - 3D Secure 2 authentication required
- `4000000000003063` - 3D Secure authentication required

### Other Scenarios
- `4000000000000077` - Charge succeeds and funds will be added directly to your available balance (bypassing your pending balance)
- `4000000000000093` - Charge succeeds with a risk_level of elevated
- `4000000000000101` - If a CVC number is provided, the cvc_check fails

## Regional Test Cards

Some cards are specific to certain regions:

### European Cards
- `4000002500001001` - Cartes Bancaires/Visa (France)
- `4000002760000016` - Visa (United Kingdom)
- `4000001240000000` - Visa (Spain)

### Asian Cards
- `4000003560000008` - Visa (Japan)
- `4000003920000003` - Visa (Hong Kong)
- `4000007020000003` - Visa (Singapore)

## Testing in Development

When testing the donation flow manually:

1. Navigate to the donation page
2. Select or enter a donation amount
3. Click "Donate"
4. In the Stripe Checkout page, use any of the test cards above
5. Fill in test information (any email, name, etc.)
6. Complete the checkout
7. You'll be redirected to the success page

## Troubleshooting

If tests are failing:

1. Verify Stripe test keys are correctly set in your environment
2. Ensure you're not using production keys with test cards
3. Check that the Stripe API version is compatible
4. Review Stripe dashboard for any webhook or configuration issues

## Additional Resources

- [Stripe Testing Documentation](https://stripe.com/docs/testing)
- [Stripe Test Cards Reference](https://stripe.com/docs/testing#cards)
- [Stripe API Keys](https://dashboard.stripe.com/test/apikeys)