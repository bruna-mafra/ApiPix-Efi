<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-cobrancas/link-de-pagamento#2-crie-um-link-de-pagamento
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
	"id" => 0
];

$body = [
	"payment_method" => "all", // "banking_billet", "credit_card", "all"
	"expire_at" => "2024-12-15",
	"request_delivery_address" => false,
	"billet_discount" => 500,
	"conditional_discount" => [
		"type" => "percentage", // "percentage", "currency"
		"value" => 500,
		"until_date" => "2024-12-10"
	],
	"card_discount" => 500,
	"message" => "This is a space\n of up to 80 characters\n to tell\n your client something"
];

try {
	$api = new EfiPay($options);
	$response = $api->defineLinkPayMethod($params, $body);

	print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
