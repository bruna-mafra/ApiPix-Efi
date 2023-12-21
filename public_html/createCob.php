<?php
require_once 'menu.php';
require_once('funcoes.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valorCobranca = $_POST['valor'];

    // Verifique se o valor é válido (você pode adicionar validações aqui)
    if (!is_numeric($valorCobranca) || $valorCobranca <= 0) {
        echo "Valor de cobrança inválido.";
    } else {
        // Geração de txid
        $txid = md5(date('Y-m-d H:i:s'));
        $idpp = rand(1, 1000);

        // Passe o valor da cobrança como parâmetro para a função geraCobranca
        geraCobranca($txid, $valorCobranca, $idpp);
        echo "Cobrança gerada com sucesso!";
    }
}
