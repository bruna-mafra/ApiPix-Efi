<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-pix/cobrancas-com-vencimento#criar-cobrança-com-vencimento
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
	"txid" => "00000000000000000000000000000000000" //  Transaction unique identifier
];

$body = [
	"calendario" => [
		"dataDeVencimento" => "2024-12-31",
		"validadeAposVencimento" => 90
	],
	"devedor" => [
		"nome" => "Francisco da Silva",
		"cpf" => "12345678909",
		// "cnpj" => "12345678000100"
		// "email" => "emaildocliente@email.com.br",
		// "logradouro" => "Alameda Souza, Numero 80, Bairro Braz",
		// "cidade" => "Recife",
		// "uf" => "PE",
		// "cep" => "70011750"
	],
	"valor" => [
		"original" => "123.45",
		"multa" => [
			"modalidade" => 2,
			"valorPerc" => "2.00"
		],
		"juros" => [
			"modalidade" => 2,
			"valorPerc" => "0.30"
		],
		"desconto" => [
			"modalidade" => 1,
			"descontoDataFixa" => [
				[
					"data" => "2024-10-15",
					"valorPerc" => "30.00"
				],
				[
					"data" => "2024-11-15",
					"valorPerc" => "15.00"
				],
				[
					"data" => "2024-12-15",
					"valorPerc" => "5.00"
				]
			]
		]
	],
	"chave" => "00000000-0000-0000-0000-000000000000", // Pix key registered in the authenticated Efí account
	"solicitacaoPagador" => "Enter the order number or identifier.",
	"infoAdicionais" => [
		[
			"nome" => "Campo 1",
			"valor" => "Informação Adicional1"
		],
		[
			"nome" => "Campo 2",
			"valor" => "Informação Adicional2"
		]
	]
];

try {
	$api = EfiPay::getInstance($options);
	$pix = $api->pixCreateDueCharge($params, $body);

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

			echo "<b>Imagem:</b><br/>";
			echo "<img src='" . $qrcode["imagemQrcode"] . "' />";
		} catch (EfiException $e) {
			print_r($e->code . "<br>");
			print_r($e->error . "<br>");
			print_r($e->errorDescription) . "<br>";
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
	} else {
		echo "<pre>" . json_encode($pix, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>";
	}
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
