<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-pix/cobrancas-imediatas#criar-cobrança-imediata-com-txid
 */

$autoload = realpath(__DIR__ . 'C:\laragon\www\EFI\Integracao_Efi\vendor\autoload.php');
if (!file_exists($autoload)) {
	die("Arquivo autoload não encontrado <code>$autoload</code>.");
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
	"txid" => "lsjdnwuehe09jeun634hh44553oo91" // Transaction unique identifier
];

$body = [
	"calendario" => [
		"expiracao" => 3600 // Charge lifetime, specified in seconds from creation date
	],
	"valor" => [
		"original" => "0.01"
	],
	"chave" => "00000000-0000-0000-0000-000000000000", // Pix key registered in the authenticated Efí account
	"solicitacaoPagador" => "Enter the order number or identifier.",
	"infoAdicionais" => [
		[
			"nome" => "Field 1",
			"valor" => "Additional information1"
		],
		[
			"nome" => "Field 2",
			"valor" => "Additional information2"
		]
	]
];

try {
	$api = EfiPay::getInstance($options);
	$pix = $api->pixCreateCharge($params, $body);

	if ($pix["txid"]) {
		$params = [
			"id" => $pix["loc"]["id"]
		];

		try {
			$qrcode = $api->pixGenerateQRCode($params);

			echo "<b>Detalhes da cobrança:</b>";
			echo "<pre>" . json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>";

			echo "<b>QR Code:</b>";
			echo "<pre>" . json_encode($qrcode, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>";

			echo "<b>Imagem:</b><br>";
			echo "<img src='" . $qrcode["imagemQrcode"] . "' />";
		} catch (EfiException $e) {
			print_r($e->code . "<br>");
			print_r($e->error . "<br>");
			print_r($e->errorDescription . "<br>");
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
	} else {
		echo "<pre>" . json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>";
	}
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription . "<br>");
} catch (Exception $e) {
	print_r($e->getMessage());
}
