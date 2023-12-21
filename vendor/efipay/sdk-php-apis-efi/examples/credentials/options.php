<?php

require_once "config.php";
/**
 * Environment
 */
$sandbox = true; // false = Production | true = Homologation

/**
 * Credentials of Production
 */
$clientIdProd = "Client_Id_Prod";
$clientSecretProd = "Client_Secret_Prod";
$pathCertificateProd = realpath(__DIR__ . ""); // Absolute path to the certificate in .pem or .p12 format

/**
 * Credentials of Homologation
 */
$clientIdHomolog = $idCliente;
$clientSecretHomolog = $secretCliente;
$pathCertificateHomolog = realpath($path . "/" . $certificate); // Absolute path to the certificate in .pem or .p12 format

/**
 * Array with credentials for sending requests
 */
$options = [
	"clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
	"clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
	"certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
	"pwdCertificate" => "", // Optional | Default = ""
	"sandbox" => $sandbox, // Optional || Default = false
	"debug" => false, // Optional
	"timeout" => 30 // Optional
];
