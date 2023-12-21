<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-pix/endpoints-exclusivos-efi#requisitar-extrato-conciliação
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
	"dataMovimento" => "2024-12-10",
	"tipoRegistros" => [
		"pixRecebido" => true,
		"pixEnviadoChave" => true,
		"pixEnviadoDadosBancarios" => true,
		"estornoPixEnviado" => true,
		"pixDevolucaoEnviada" => true,
		"pixDevolucaoRecebida" => true,
		"tarifaPixEnviado" => true,
		"tarifaPixRecebido" => true,
		"estornoTarifaPixEnviado" => true,
		"saldoDiaAnterior" => true,
		"saldoDia" => true,
		"transferenciaEnviada" => true,
		"transferenciaRecebida" => true,
		"estornoTransferenciaEnviada" => true,
		"tarifaTransferenciaEnviada" => true,
		"estornoTarifaTransferenciaEnviada" => true,
		"estornoTarifaPixRecebido" => true
	]
];

try {
	$api = EfiPay::getInstance($options);
	$response = $api->createReport($params = [], $body);

	print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	print_r($e->code . "<br>");
	print_r($e->error . "<br>");
	print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	print_r($e->getMessage());
}
