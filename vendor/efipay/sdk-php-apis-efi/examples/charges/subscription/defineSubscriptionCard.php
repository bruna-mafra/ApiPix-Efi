<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-cobrancas/assinatura#2-defina-a-forma-de-pagamento-da-assinatura-e-os-dados-do-cliente
 */

$autoload = realpath(__DIR__ . "/../../../vendor/autoload.php");
if (!file_exists($autoload)) {
    die("Autoload file not found or on path <code>$autoload</code>.");
}
require_once $autoload;

use Efi\Exception\EfiException;
use Efi\EfiPay;

$options = __DIR__ . "/../../credentials/options.php";
if (!file_exists($options)) {
	die("Options file not found or on path <code>$options</code>.");
}
require $options;

$params = [
	"id" => 0 // subscription id
];

$paymentToken = "insert_here_the_payment_token_referring_to_card_data";

$customer = [
	"name" => "Gorbadoc Oldbuck",
	"cpf" => "04267484171",
	"phone_number" => "5144916523",
	"email" => "oldbuck@server.com.br",
	"birth" => "1977-01-15"
];

$billingAddress = [
	"street" => "Av. JK",
	"number" => 909,
	"neighborhood" => "Bauxita",
	"zipcode" => "35400000",
	"city" => "Ouro Preto",
	"state" => "MG",
];

$body = [
	"payment" => [
		"credit_card" => [
			"billing_address" => $billingAddress,
			"payment_token" => $paymentToken,
			"customer" => $customer
		]
	]
];

try {
	$api = new EfiPay($options);
	$response = $api->defineSubscriptionPayMethod($params, $body);

	print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
