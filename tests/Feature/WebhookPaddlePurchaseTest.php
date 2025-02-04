<?php

use App\Jobs\HandlePaddlePurchaseJob;
use Illuminate\Support\Carbon;
use Spatie\WebhookClient\Models\WebhookCall;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertSame;

it('can create a valid paddle webhook signature', function () {
    //Arrange
    $originalTimestamp = 1718139311;
    [$originalArrBody, $originalSigHeader, $originalRawJsonBody] = getValidPaddleWebhookRequest();

    //Act & Assert
    [$body, $header] = generateValidSignedPaddleWebhookRequest($originalArrBody, $originalTimestamp);
    assertSame(json_encode($body), $originalRawJsonBody);
    assertSame($header, $originalSigHeader);

});
it('stores a paddle purchase request', function () {
    //Arrange
    Queue::fake();
    assertDatabaseCount(WebhookCall::class, 0);
    [$arrData] = getValidPaddleWebhookRequest();

    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);

    //Act
    postJson('webhooks', $requestBody, $requestHeaders);

    //Assert
    assertDatabaseCount(WebhookCall::class, 1);

});

it('does not stores invalid paddle purchase request', function () {
    //Arrange
    assertDatabaseCount(WebhookCall::class, 0);

    //Act
    post('webhooks', getInvalidPaddleWebhookRequest());

    //Assert
    assertDatabaseCount(WebhookCall::class, 0);

});

it('dispatches a job for a valid paddle request', function () {
    // Arrange
    Queue::fake();

    // Act
    [$arrData] = getValidPaddleWebhookRequest();
    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);
    postJson('webhooks', $requestBody, $requestHeaders);

    // Assert
    Queue::assertPushed(HandlePaddlePurchaseJob::class);

});

it('does not dispatch a job for invalid paddle request', function () {
    // Arrange
    Queue::fake();

    // Act
    post('webhooks', getInvalidPaddleWebhookRequest());

    // Assert
    Queue::assertNotPushed(HandlePaddlePurchaseJob::class);
});

function getValidPaddleWebhookRequest(): array
{
    $sigHeader = ['Paddle-Signature' => 'ts=1718139311;h1=10b60215868ee114bd3f20a640c873cb883a189c8f6ab37c9335ea586ee7695e'];
    $parsedData = [
        'event_id' => 'evt_01jhqxaq6gytsrh5de9f06q8kb',
        'event_type' => 'transaction.completed',
        'occurred_at' => '2025-01-16T15:57:21.489078Z',
        'notification_id' => 'ntf_01jhqxaqaeck14yjfse5y734nn',
        'data' => [
            'id' => 'txn_01jhqx6c3mx3c13zg05qdbe5za',
            'items' => [
                [
                    'price' => [
                        'id' => 'pri_01jhqsmchvxh9rhh5fx9e3h5ck',
                        'name' => 'Pago Laravel For Beginners',
                        'type' => 'standard',
                        'status' => 'active',
                        'quantity' => [
                            'maximum' => 10000,
                            'minimum' => 1,
                        ],
                        'tax_mode' => 'account_setting',
                        'created_at' => '2025-01-16T14:52:43.963362Z',
                        'product_id' => 'pro_01jhqsgb6m3y247k54ktpph9ht',
                        'unit_price' => [
                            'amount' => '1500',
                            'currency_code' => 'EUR',
                        ],
                        'updated_at' => '2025-01-16T14:52:43.963363Z',
                        'custom_data' => null,
                        'description' => 'Pago unico',
                        'trial_period' => null,
                        'billing_cycle' => null,
                        'unit_price_overrides' => [
                        ],
                    ],
                    'price_id' => 'pri_01jhqsmchvxh9rhh5fx9e3h5ck',
                    'quantity' => 1,
                    'proration' => null,
                ],
            ],
            'origin' => 'web',
            'status' => 'completed',
            'details' => [
                'totals' => [
                    'fee' => '124',
                    'tax' => '260',
                    'total' => '1500',
                    'credit' => '0',
                    'balance' => '0',
                    'discount' => '0',
                    'earnings' => '1116',
                    'subtotal' => '1240',
                    'grand_total' => '1500',
                    'currency_code' => 'EUR',
                    'credit_to_balance' => '0',
                ],
                'line_items' => [
                    [
                        'id' => 'txnitm_01jhqx7t56ynxn99q980myswde',
                        'totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'item_id' => null,
                        'product' => [
                            'id' => 'pro_01jhqsgb6m3y247k54ktpph9ht',
                            'name' => 'Laravel For Beginners',
                            'type' => 'standard',
                            'status' => 'active',
                            'image_url' => null,
                            'created_at' => '2025-01-16T14:50:31.508Z',
                            'updated_at' => '2025-01-16T14:50:31.508Z',
                            'custom_data' => [
                                'Product' => 'one',
                            ],
                            'description' => 'Laravel For Beginners',
                            'tax_category' => 'standard',
                        ],
                        'price_id' => 'pri_01jhqsmchvxh9rhh5fx9e3h5ck',
                        'quantity' => 1,
                        'tax_rate' => '0.21',
                        'unit_totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'is_tax_exempt' => false,
                        'revised_tax_exempted' => false,
                    ],
                ],
                'payout_totals' => [
                    'fee' => '125',
                    'tax' => '262',
                    'total' => '1512',
                    'credit' => '0',
                    'balance' => '0',
                    'discount' => '0',
                    'earnings' => '1125',
                    'fee_rate' => '0.05',
                    'subtotal' => '1250',
                    'grand_total' => '1512',
                    'currency_code' => 'USD',
                    'exchange_rate' => '1.0081210999999999',
                    'credit_to_balance' => '0',
                ],
                'tax_rates_used' => [
                    [
                        'totals' => [
                            'tax' => '260',
                            'total' => '1500',
                            'discount' => '0',
                            'subtotal' => '1240',
                        ],
                        'tax_rate' => '0.21',
                    ],
                ],
                'adjusted_totals' => [
                    'fee' => '124',
                    'tax' => '260',
                    'total' => '1500',
                    'earnings' => '1116',
                    'subtotal' => '1240',
                    'grand_total' => '1500',
                    'currency_code' => 'EUR',
                ],
            ],
            'checkout' => [
                'url' => 'https://localhost?_ptxn=txn_01jhqx6c3mx3c13zg05qdbe5za',
            ],
            'payments' => [
                [
                    'amount' => '1500',
                    'status' => 'captured',
                    'created_at' => '2025-01-16T15:57:16.90802Z',
                    'error_code' => null,
                    'captured_at' => '2025-01-16T15:57:18.869886Z',
                    'method_details' => [
                        'card' => [
                            'type' => 'visa',
                            'last4' => '4242',
                            'expiry_year' => 2025,
                            'expiry_month' => 5,
                            'cardholder_name' => 'pepa',
                        ],
                        'type' => 'card',
                    ],
                    'payment_method_id' => 'paymtd_01jhqxajps23fctdvyp8ch5bdt',
                    'payment_attempt_id' => '46174673-de23-4ce3-a30f-0ac7d8f68569',
                    'stored_payment_method_id' => 'ecb66868-f93b-48e6-893d-7bbc09fda66a',
                ],
            ],
            'billed_at' => '2025-01-16T15:57:19.166418Z',
            'address_id' => 'add_01jhqx7svn27d3gb2336459ct9',
            'created_at' => '2025-01-16T15:54:59.101802Z',
            'invoice_id' => 'inv_01jhqxan5sbhkk28mt66m6jtaw',
            'updated_at' => '2025-01-16T15:57:21.277713877Z',
            'business_id' => null,
            'custom_data' => null,
            'customer_id' => 'ctm_01jhqx7sv8s0w93p9c993fbdj3',
            'discount_id' => null,
            'receipt_data' => null,
            'currency_code' => 'EUR',
            'billing_period' => null,
            'invoice_number' => '10632-10001',
            'billing_details' => null,
            'collection_mode' => 'automatic',
            'subscription_id' => null,
        ],
    ];

    $rawJsonBody = '{"event_id":"evt_01jhqxaq6gytsrh5de9f06q8kb","event_type":"transaction.completed","occurred_at":"2025-01-16T15:57:21.489078Z","notification_id":"ntf_01jhqxaqaeck14yjfse5y734nn","data":{"id":"txn_01jhqx6c3mx3c13zg05qdbe5za","items":[{"price":{"id":"pri_01jhqsmchvxh9rhh5fx9e3h5ck","name":"Pago Laravel For Beginners","type":"standard","status":"active","quantity":{"maximum":10000,"minimum":1},"tax_mode":"account_setting","created_at":"2025-01-16T14:52:43.963362Z","product_id":"pro_01jhqsgb6m3y247k54ktpph9ht","unit_price":{"amount":"1500","currency_code":"EUR"},"updated_at":"2025-01-16T14:52:43.963363Z","custom_data":null,"description":"Pago unico","trial_period":null,"billing_cycle":null,"unit_price_overrides":[]},"price_id":"pri_01jhqsmchvxh9rhh5fx9e3h5ck","quantity":1,"proration":null}],"origin":"web","status":"completed","details":{"totals":{"fee":"124","tax":"260","total":"1500","credit":"0","balance":"0","discount":"0","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR","credit_to_balance":"0"},"line_items":[{"id":"txnitm_01jhqx7t56ynxn99q980myswde","totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"item_id":null,"product":{"id":"pro_01jhqsgb6m3y247k54ktpph9ht","name":"Laravel For Beginners","type":"standard","status":"active","image_url":null,"created_at":"2025-01-16T14:50:31.508Z","updated_at":"2025-01-16T14:50:31.508Z","custom_data":{"Product":"one"},"description":"Laravel For Beginners","tax_category":"standard"},"price_id":"pri_01jhqsmchvxh9rhh5fx9e3h5ck","quantity":1,"tax_rate":"0.21","unit_totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"is_tax_exempt":false,"revised_tax_exempted":false}],"payout_totals":{"fee":"125","tax":"262","total":"1512","credit":"0","balance":"0","discount":"0","earnings":"1125","fee_rate":"0.05","subtotal":"1250","grand_total":"1512","currency_code":"USD","exchange_rate":"1.0081210999999999","credit_to_balance":"0"},"tax_rates_used":[{"totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"tax_rate":"0.21"}],"adjusted_totals":{"fee":"124","tax":"260","total":"1500","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR"}},"checkout":{"url":"https:\/\/localhost?_ptxn=txn_01jhqx6c3mx3c13zg05qdbe5za"},"payments":[{"amount":"1500","status":"captured","created_at":"2025-01-16T15:57:16.90802Z","error_code":null,"captured_at":"2025-01-16T15:57:18.869886Z","method_details":{"card":{"type":"visa","last4":"4242","expiry_year":2025,"expiry_month":5,"cardholder_name":"pepa"},"type":"card"},"payment_method_id":"paymtd_01jhqxajps23fctdvyp8ch5bdt","payment_attempt_id":"46174673-de23-4ce3-a30f-0ac7d8f68569","stored_payment_method_id":"ecb66868-f93b-48e6-893d-7bbc09fda66a"}],"billed_at":"2025-01-16T15:57:19.166418Z","address_id":"add_01jhqx7svn27d3gb2336459ct9","created_at":"2025-01-16T15:54:59.101802Z","invoice_id":"inv_01jhqxan5sbhkk28mt66m6jtaw","updated_at":"2025-01-16T15:57:21.277713877Z","business_id":null,"custom_data":null,"customer_id":"ctm_01jhqx7sv8s0w93p9c993fbdj3","discount_id":null,"receipt_data":null,"currency_code":"EUR","billing_period":null,"invoice_number":"10632-10001","billing_details":null,"collection_mode":"automatic","subscription_id":null}}';

    return [$parsedData, $sigHeader, $rawJsonBody];
}

function generateValidSignedPaddleWebhookRequest(array $data, ?int $timestamp = null): array
{
    $ts = $timestamp ?? Carbon::now()->unix();
    $secret = config('services.paddle.notification-endpoint-secret-key');

    $rawJsonBody = json_encode($data);

    $calculatedSig = hash_hmac('sha256', "{$ts}:{$rawJsonBody}", $secret);

    $header = [
        'Paddle-Signature' => "ts={$ts};h1={$calculatedSig}",
    ];

    return [$data, $header];
}

function getInvalidPaddleWebhookRequest(): array
{
    return [];
}
