<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require '../../app/database/connection.php';

use OpenBoleto\Banco\Nubank;
use OpenBoleto\Agente;

$conn = conecta_db();

$boleto_id = $_GET['id'];
$query = "SELECT b.boleto_id, b.numero_documento, b.valor, b.banco, b.codigo_barras, b.linha_digitavel, b.vencimento, l.empresa_nome, l.empresa_cnpj FROM tb_boletos b JOIN tb_contrato c ON b.contrato_id = c.contrato_id JOIN tb_locatarios l ON l.empresa_id = c.empresa_id WHERE b.boleto_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $boleto_id);
$stmt->execute();
$resultado = $stmt->get_result();
$boleto = $resultado->fetch_object();
$stmt->close();

$pagador = new Agente($boleto->empresa_nome, $boleto->empresa_cnpj);
$cedente = new Agente('Tijucas Open', '86.398.751/0001-23', 'Rua Fictícia 123', 'Tijucas do Sul', 'PR', '00000-000');

$vencimento = new DateTime($boleto->vencimento);
$valor = $boleto->valor;
$numeroDocumento = $boleto->numero_documento;
$numero = intval(substr($numeroDocumento, -3));

$boleto = new Nubank([
    'dataVencimento' => $vencimento,
    'valor' => $valor,
    'numero' => $numero,
    'pagador' => $pagador,
    'cedente' => $cedente,
    'agencia' => 1234,
    'carteira' => 1,
    'conta' => 123456,
    'numeroDocumento' => $numeroDocumento
]);

$boleto->download("Boleto_{$numeroDocumento}.pdf");
?>