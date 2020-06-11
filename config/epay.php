<?php
return [
    'pay_test_mode' => false,
    'MERCHANT_CERTIFICATE_ID'   => 'c183e9f8',
    'MERCHANT_NAME'             => 'start-time.kz',
    'PRIVATE_KEY_PATH'          => storage_path('paysys/cert.prv'),
    'PRIVATE_KEY_PASS'          => 'WDfUveEf9i3',
    'XML_TEMPLATE_FN'           => storage_path('paysys/template.xml'),
    'XML_COMMAND_TEMPLATE_FN'   => storage_path('paysys/command_template.xml'),
    'PUBLIC_KEY_PATH'           => storage_path('paysys/kkbca.pem'),
    'MERCHANT_ID'               => '98024241',
    // Линк для возврата покупателя в магазин (на сайт) после успешного проведения оплаты
    'EPAY_BACK_LINK'            => env('FRONTEND_URL').'/main',
    // Линк для отправки результата авторизации в магазин.
    'EPAY_POST_LINK'            => env('APP_URL').'/api/v1/payment/success',
    // Линк для отправки неудачного результата авторизации либо информации об ошибке в магазин.
    'EPAY_FAILURE_POST_LINK'    => env('APP_URL').'/api/v1/payment/fail',

    'EPAY_FORM_TEMPLATE'        => 'default.xsl',
];
