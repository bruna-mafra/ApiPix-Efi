<!DOCTYPE html>
<html>

<head>
    <title>Exibir QR Code</title>

</head>

<body>
    <div class="container mt-4">
        <h2>Exibir QR Code</h2>
        <form method="POST" action="searchCob.php" id="formQrCode" class="row g-3">
            <div class="col-12">
                <label for="idpp_recebido" class="form-label">ID do Pedido:</label>
                <input type="text" name="idpp_recebido" id="idpp_recebido" class="form-control" required>
            </div>
            <div class="col-auto">
                <input type="submit" value="Exibir QR Code" class="btn btn-primary">
            </div>
        </form>
    </div>

</body>

</html>