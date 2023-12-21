<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-cobrancas/boleto/#2-associar-à-forma-de-pagamento-via-boleto
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

$customer = [
	"name" => "Gorbadoc Oldbuck",
	"cpf" => "94271564656"
];

$body = [
	"payment" => [
		"banking_billet" => [
			"expire_at" => "2024-12-10",
			"customer" => $customer
		]
	]
];

try {
	$api = new EfiPay($options);
	$response = $api->definePayMethod($params, $body);

	print_r("<pre>Second step:<br>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
