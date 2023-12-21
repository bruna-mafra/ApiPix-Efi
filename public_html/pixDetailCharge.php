<?php

/**
 * Detailed endpoint documentation
 * https://dev.efipay.com.br/docs/api-pix/cobrancas-imediatas#consultar-cobrança
 */

require_once __DIR__ . "/../credenciais/config.php";

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
	die("Autoload file not found or on path <code>$autoload</code>.");
}
require_once $autoload;

use Efi\Exception\EfiException;
use Efi\EfiPay;

$options = __DIR__ . "/../credenciais/credenciais.php";
if (!file_exists($options)) {
	die("Options file not found or on path <code>$options</code>.");
}
require $options;

$params = [
	"txid" => "$txid"
];

try {
	$api = EfiPay::getInstance($options);
	$response = $api->pixDetailCharge($params);
	// print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
} catch (EfiException $e) {
	// print_r($e->code . "<br>");
	// print_r($e->error . "<br>");
	// print_r($e->errorDescription) . "<br>";
} catch (Exception $e) {
	// print_r($e->getMessage());
}
if (!$response) {
	print_r($e->getMessage());
	exit();
	// exit('Erro na consulta do txid');
} else {

	$data_criacao = converteData($response['calendario']['criacao']);
	$data_expiracao = geraDataExpiracao($data_criacao, 864000);
	$valorRecebido = doubleval($response['pix'][0]['valor']);
	$valorRecebido = round((100 - TARIFA_PIX_EFI) / 100 * $valorRecebido, 2);

	$dados_resposta = [
		'dataCriacao' => $data_criacao,
		'dataExpiracao' => $data_expiracao,
		'txid' => $response['txid'],
		'revisao' => $response['revisao'],
		'locId' => $response['loc']['id'],
		'locLocation' => $response['loc']['location'],
		'tipoCob' => $response['loc']['tipoCob'],
		'locCriacao' => converteData($response['loc']['criacao']),
		'status' => $response['status'],
		'valorOriginal' => doubleval($response['valor']['original']),
		'chave' => $response['chave'],
		'solicitacaoPagador' => $response['solicitacaoPagador'],
		'endToEndId' => $response['pix'][0]['endToEndId'],
		'dataPagamento' => converteData($response['pix'][0]['horario']),
		'valorReal' => $valorRecebido
	];
}
