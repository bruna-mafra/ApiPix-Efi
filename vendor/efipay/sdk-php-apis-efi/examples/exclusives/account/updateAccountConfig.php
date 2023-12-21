<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-pix/endpoints-exclusivos-efi#criarmodificar-configurações-da-conta
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

$body = [
	"pix" => [
		"receberSemChave" => true,
		"chaves" => [
			"00000000-0000-0000-0000-000000000000" => [
				"recebimento" => [
					"txidObrigatorio" => false,
					"qrCodeEstatico" => [
						"recusarTodos" => false
					],
					"webhook" => [
						"notificacao" => [
							"tarifa" => true
						]
					]
				]
			],
			"11111111-1111-1111-1111-111111111111" => [
				"recebimento" => [
					"txidObrigatorio" => false,
					"qrCodeEstatico" => [
						"recusarTodos" => false
					]
				]
			]
		]
	]
];

try {
	$api = EfiPay::getInstance($options);
	$response = $api->updateAccountConfig($params = [], $body);

	print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
