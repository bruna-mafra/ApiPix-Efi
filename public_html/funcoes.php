<?php
// Arquivo apenas de funções
require_once __DIR__ . "/../credenciais/config.php";

use Efi\Exception\EfiException;
use Efi\EfiPay;


function geraCobranca($txid, $valorCobranca, $idpp)
{
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        die("Autoload file not found or on path <code>$autoload</code>.");
    }
    require_once $autoload;

    $options = __DIR__ . "/../credenciais/credenciais.php";
    if (!file_exists($options)) {
        die("Options file not found or on path <code>$options</code>.");
    }
    require_once $options;

    $params = [
        "txid" => $txid
    ];

    $descricao = "Descrição aqui";

    $body = [
        "calendario" => [
            "expiracao" => TEMPO_EXPIRACAO
        ],
        "valor" => [
            "original" => $valorCobranca
        ],
        "chave" => CHAVE_ALEATORIA, // Chave PIX autenticada na Efí
        "solicitacaoPagador" => $descricao,
    ];

    try {
        $api = EfiPay::getInstance($options);
        $pix = $api->pixCreateCharge($params, $body);

        $data_criacao = converteData($pix["calendario"]["criacao"]);
        $data_expiracao = geraDataExpiracao($data_criacao, 864000);
        $loc_criacao = converteData($pix['loc']['criacao']);

        //copia e cola
        if ($pix["txid"]) {
            $params = [
                "id" => $pix["loc"]["id"]
            ];

            try {
                $qrcode = $api->pixGenerateQRCode($params);
                $copiaEcola = json_encode($qrcode, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } catch (EfiException $e) {
                print_r($e->code . "<br>");
                print_r($e->error . "<br>");
                print_r($e->errorDescription . "<br>");
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        } else {
        }

        $copiaEcola = json_decode($copiaEcola, true);
        $copiaEcola = $copiaEcola['qrcode'];


        $arrayDados = [
            'idpp' => $idpp,
            'status' => $pix['status'],
            'txid' => $pix['txid'],
            'valorOriginal' => doubleval($pix["valor"]["original"]),
            'dataCriacao' => $data_criacao,
            'dataExpiracao' => $data_expiracao,
            'chave' => $pix['chave'],
            'pixCopiaECola' => $copiaEcola,
            'revisao' => intval($pix['revisao']),
            'locId' => intval($pix["loc"]["id"]),
            'locLocation' => $pix["loc"]["location"],
            'solicitacaoPagador' => $pix['solicitacaoPagador'],
            'tipoCob' => $pix["loc"]["tipoCob"],
            'locCriacao' => $loc_criacao,
        ];

        insereDados($arrayDados);
    } catch (EfiException $e) {
        print_r($e->code . "<br>");
        print_r($e->error . "<br>");
        print_r($e->errorDescription . "<br>");
    } catch (Exception $e) {
        print_r($e->getMessage());
    }
}

function converteData($data)
{
    $dataHoraFormatada = new DateTime($data, new DateTimeZone('UTC'));
    $dataHoraFormatada->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    return $dataHoraFormatada->format('Y-m-d H:i:s');
}

function geraDataExpiracao($data, $time)
{
    $dateTime = new DateTime($data);
    $dateTime->add(new DateInterval("PT" . $time . "S"));
    return $dateTime->format('Y-m-d H:i:s');
}

function insereDados($dataArray)
{
    $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Montar a consulta SQL dinamicamente
    $columns = implode(', ', array_keys($dataArray));
    $values = implode("', '", $dataArray);

    $sql = "INSERT INTO pix ($columns) VALUES ('$values')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro inserido com sucesso.";
    } else {
        echo "Erro na inserção: " . $conn->error;
    }
    $conn->close();
}

function atualizaDados($dataArray, $condition)
{
    $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Construir a parte SET da consulta SQL
    $setClause = implode(', ', array_map(function ($key, $value) {
        return "$key = '$value'";
    }, array_keys($dataArray), $dataArray));

    // Executar a consulta SQL
    $sql = "UPDATE pix SET $setClause WHERE $condition";
    $result = $conn->query($sql);

    // Verificar se a consulta foi bem-sucedida
    if ($result === TRUE) {
        echo "Registro atualizado com sucesso.";
    } else {
        echo "Erro na atualização: " . $conn->error;
    }

    // Fechar a conexão
    $conn->close();
}

function obtemLocId($idpp)
{
    // Conexão com o banco de dados
    $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // SQL para obter o ID da cobrança com base no ID do pedido
    $sql = "SELECT locId FROM pix WHERE idpp = '$idpp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["locId"];
    } else {
        return false;
    }

    $conn->close();
}

function exibeQRCode($idpp)
{
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    $optionsPath = __DIR__ . "/../credenciais/credenciais.php";

    if (!file_exists($autoloadPath)) {
        die("Autoload file not found or on path <code>$autoloadPath</code>.");
    }
    if (!file_exists($optionsPath)) {
        die("Options file not found or on path <code>$optionsPath</code>.");
    }

    require_once $autoloadPath;
    require_once $optionsPath;

    $idCobranca = obtemLocId($idpp);

    if ($idCobranca) {
        $params = ["id" => $idCobranca];

        try {
            $api = EfiPay::getInstance($options);
            $qrcode = $api->pixGenerateQRCode($params);

            echo "<pre>" . json_encode($qrcode, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>";
            echo "<img src='" . $qrcode["imagemQrcode"] . "' />";
        } catch (EfiException $e) {
            exibeErro($e->code, $e->error, $e->errorDescription);
        } catch (Exception $e) {
            exibeErro($e->getCode(), $e->getMessage());
        }
    } else {
        echo "ID da cobrança não encontrado para o pedido.";
    }
}

function exibeErro($code, $error, $description = "")
{
    print_r("$code<br>");
    print_r("$error<br>");
    print_r("$description<br>");
}
