<?php

require_once "config.php";

$sandbox = SANDBOX_ATIVO; // false = Production | true = Homologation

$clientIdProd = CLIENT_ID;
$clientSecretProd = CLIENT_SECRET;
$pathCertificateProd = realpath(__DIR__ . "/" . CERTIFICATE);

$clientIdHomolog = CLIENT_ID;
$clientSecretHomolog = CLIENT_SECRET;
$pathCertificateHomolog = realpath(__DIR__ . "/" . CERTIFICATE);

$options = [
	"clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
	"clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
	"certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
	"pwdCertificate" => "", // Opcional | Default = ""
	"sandbox" => $sandbox, // Opcional | Default = false
	"debug" => false, // Opcional
	"timeout" => 30 // Opcional
];
