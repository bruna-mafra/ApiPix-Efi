<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-cobrancas/boleto/#criação-de-boleto-bolix-em-one-step-um-passo
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

$items = [
	[
		"name" => "Product 1",
		"amount" => 1,
		"value" => 9990
	],
	[
		"name" => "Product 2",
		"amount" => 1,
		"value" => 1500
	],
];

$shippings = [
	[
		"name" => "Shipping to City",
		"value" => 1200
	]
];

$metadata = [
	"custom_id" => "Order_00001",
	"notification_url" => "https://your-domain.com.br/notification/"
];

$customer = [
	"name" => "Gorbadoc Oldbuck",
	"cpf" => "94271564656",
	// "email" => "",
	// "phone_number" => "",
	// "birth" => "",
	// "address" => [
	// 	"street" => "",
	// 	"number" => "",
	// 	"neighborhood" => "",
	// 	"zipcode" => "",
	// 	"city" => "",
	// 	"complement" => "",
	// 	"state" => "",
	// 	"juridical_person" => "",
	// 	"corporate_name" => "",
	// 	"cnpj" => ""
	// ],
];

$discount = [
	"type" => "currency", // "currency", "percentage"
	"value" => 599
];

$conditional_discount = [
	"type" => "percentage", // "currency", "percentage"
	"value" => 500,
	"until_date" => "2024-12-20"
];

$configurations = [
	"fine" => 200,
	"interest" => 33
];

$bankingBillet = [
	"expire_at" => "2024-12-20",
	"message" => "This is a space\n of up to 80 characters\n to tell\n your client something",
	"customer" => $customer,
	"discount" => $discount,
	"conditional_discount" => $conditional_discount
];

$payment = [
	"banking_billet" => $bankingBillet
];

$body = [
	"items" => $items,
	"shippings" => $shippings,
	"metadata" => $metadata,
	"payment" => $payment
];

try {
	$api = new EfiPay($options);
	$response = $api->createOneStepCharge($params = [], $body);

	print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
