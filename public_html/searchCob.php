<?php
require_once 'funcoes.php';
require_once 'menu.php';
include 'searchCobForm.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idpp_recebido'])) {
        $idpp_recebido = $_POST['idpp_recebido'];
        exibeQRCode($idpp_recebido);
    } else {
        echo "ID do Pedido não fornecido.";
    }
}

if (isset($_GET['idpp_recebido'])) {
    $idpp_recebido = $_GET['idpp_recebido'];
    exibeQRCode($idpp_recebido);
}
