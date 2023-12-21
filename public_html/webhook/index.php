<?php
require_once('../../credenciais/config.php');
require_once('../funcoes.php');

$ip = $_SERVER['REMOTE_ADDR'];
if (!in_array($ip, $ipsPermitidos)) {
  exit("ACCESS DENIED");
}
$metodo = $_SERVER['REQUEST_METHOD'];
if ($metodo != 'POST')
  exit("ACCESS DENIED");

// Obtém o body e parâmetros da requisição
$parametros = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$body = json_decode(file_get_contents('php://input'), true);

// Reconhece o txid do corpo da requisicao armazenado em $body
$txid = $body['pix'][0]['txid'];

$response = null;
require "../pixDetailCharge.php"; // Obtém os dados da cobrança

atualizaDados($dados_resposta, "txid = '$txid'");
