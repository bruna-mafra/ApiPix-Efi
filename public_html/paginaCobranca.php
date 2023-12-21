<?php require_once 'menu.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Formulário de Cobrança</title>
</head>

<body>
    <div class="container mt-4">
        <h2>Criar Cobrança</h2>
        <form action="createCob.php" method="POST" class="row g-3">
            <div class="col-12">
                <label for="valor" class="form-label">Valor da Cobrança:</label>
                <input type="text" name="valor" id="valor" class="form-control" placeholder="Informe o valor" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Gerar Cobrança</button>
            </div>
        </form>
    </div>
</body>

</html>