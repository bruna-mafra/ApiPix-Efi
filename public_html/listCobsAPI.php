<!DOCTYPE html>
<html>

<head>
</head>

<body>

	<?php

	require_once 'menu.php';

	/**
	 * Detailed endpoint documentation
	 * https://dev.efipay.com.br/docs/api-pix/cobrancas-imediatas#consultar-lista-de-cobranças
	 */

	$autoload = realpath(__DIR__ . "/../vendor/autoload.php");
	if (!file_exists($autoload)) {
		die("Autoload file not found or on path <code>$autoload</code>.");
	}
	require_once $autoload;

	use Efi\Exception\EfiException;
	use Efi\EfiPay;

	$options = __DIR__ . "/../credenciais/credenciais.php";
	if (!file_exists($options)) {
		die("Options file not found or on path <code>$options</code>.");
	}
	require $options;

	$params = [
		"inicio" => "2023-01-22T00:00:00Z",
		"fim" => "2024-12-31T23:59:59Z",
		// "status" => "CONCLUIDA", // "ATIVA","CONCLUIDA", "REMOVIDA_PELO_USUARIO_RECEBEDOR", "REMOVIDA_PELO_PSP"
		// "cpf" => "12345678909", // Filter by payer's CPF. It cannot be used at the same time as the CNPJ.
		// "cnpj" => "12345678909", // Filter by payer's CNPJ. It cannot be used at the same time as the CPF.
		// "paginacao.paginaAtual" => 1,
		// "paginacao.itensPorPagina" => 10
	];

	try {
		$api = EfiPay::getInstance($options);
		$response = $api->pixListCharges($params);

		if (isset($response['cobs']) && count($response['cobs']) > 0) {
			echo '<div class="container-fluid mt-4 p-5">';
			echo '<table class="table table-striped" id="tableList">';
			echo '<thead><tr>';
			echo '<th>Status</th>';
			echo '<th>Txid</th>';
			echo '<th>EndToEndId</th>';
			echo '<th>Valor Original</th>';
			echo '<th>Data de Criação</th>';
			echo '<th>Data de Pagamento</th>';
			echo '<th>Data de Expiração</th>';
			echo '<th>Chave</th>';
			echo '</tr></thead>';
			echo '<tbody>';

			foreach ($response['cobs'] as $cob) {
				echo '<tr>';
				echo '<td>' . $cob['status'] . '</td>';
				echo '<td>' . $cob['txid'] . '</td>';
				if (isset($cob['pix'])) {
					echo '<td>' . $cob['pix'][0]['endToEndId'] . '</td>';
				} else {
					echo '<td>Inexistente</td>';
				}
				echo '<td>' . $cob['valor']['original'] . '</td>';
				echo '<td>' . $cob['calendario']['criacao'] . '</td>';
				if (isset($cob['pix'])) {
					echo '<td>' . $cob['pix'][0]['horario'] . '</td>';
				} else {
					echo '<td>Não pago</td>';
				}
				echo '<td>' . $cob['calendario']['expiracao'] . '</td>';
				echo '<td>' . $cob['chave'] . '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table></div>';
		} else {
			echo "Nenhum registro encontrado.";
		}
	} catch (EfiException $e) {
		print_r($e->code . "<br>");
		print_r($e->error . "<br>");
		print_r($e->errorDescription) . "<br>";
	} catch (Exception $e) {
		print_r($e->getMessage());
	}
	?>

</body>

</html>