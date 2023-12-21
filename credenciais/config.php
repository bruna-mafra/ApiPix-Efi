<?php

define("SANDBOX_ATIVO", true);

if (SANDBOX_ATIVO == true) {
    define("CLIENT_ID", "***");
    define("CLIENT_SECRET", "***");
    define("CERTIFICATE", "***.pem");
} else {
    // Se $SANDBOX_ATIVO for falso (false), as constantes são definidas para o ambiente de produção.
    define("CLIENT_ID", "***");
    define("CLIENT_SECRET", "***");
    define("CERTIFICATE", "***.pem");
}

define("CHAVE_ALEATORIA", "***");
define("URL_WEBHOOK", "***");
define("TEMPO_EXPIRACAO", 864000); // Em segundos

$ipsPermitidos = [
    '***',
    '127.0.0.1'
];

function verificaUrl() // Se retornar true, estamos no laragon
{

    $host = $_SERVER['HTTP_HOST'];

    // Verifica se a URL contém a string "efi.test"
    if (strpos($host, 'efi.test') !== false) {
        return true;
    } else {
        return false;
    }
}

if (verificaUrl() == false) {
    define("SERVER_NAME", "***");
    define("USER_NAME", "***");
    define("PASSWORD", "***");
    define("DATABASE", "***");
} else {
    define("SERVER_NAME", "***");
    define("USER_NAME", "***");
    define("PASSWORD", "***");
    define("DATABASE", "***");
}
define("TARIFA_PIX_EFI", 1.19);

?>