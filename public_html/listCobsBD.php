<?php
require_once 'menu.php';
require_once('../credenciais/config.php');

$conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta SQL para buscar registros
$sql = "SELECT * FROM pix";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <table class="table table-striped" id="tableList">
        <thead>
            <tr>
                <th>ID</th>
                <th>IDPP</th>
                <th>Status</th>
                <th>Txid</th>
                <th>EndToEndId</th>
                <th>Valor Original</th>
                <th>Valor Recebido</th>
                <th>Data de criação</th>
                <th>Data de pagamento</th>
                <th>Data de expiração</th>
                <th>Copia e cola</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td><a href='searchCob.php?idpp_recebido=" . $row["idpp"] . "'>" . $row["idpp"] . "</a></td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["txid"] . "</td>";
                    echo "<td>" . $row["endToEndId"] . "</td>";
                    echo "<td>" . $row["valorOriginal"] . "</td>";
                    echo "<td>" . $row["valorReal"] . "</td>";
                    echo "<td>" . $row["dataCriacao"] . "</td>";
                    echo "<td>" . $row["dataPagamento"] . "</td>";
                    echo "<td>" . $row["dataExpiracao"] . "</td>";
                    echo "<td>" . $row["pixCopiaECola"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='13'>Nenhum registro encontrado.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</body>

</html>