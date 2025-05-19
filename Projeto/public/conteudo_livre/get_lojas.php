<?php
require '../../app/database/connection.php';
$conn = conecta_db();

$query = "SELECT loja_id, espaco_id, loja_nome, loja_telefone, loja_andar, loja_tipo FROM tb_lojas";
$result = $conn->query($query);

$lojas = [];

while ($linha = $result->fetch_object()) {
    $lojas[] = [
        'id' => $linha->loja_id,
        'espaco_id' => $linha->espaco_id,
        'nome' => $linha->loja_nome,
        'telefone' => $linha->loja_telefone,
        'andar' => $linha->loja_andar,
        'tipo' => $linha->loja_tipo,
    ];
}

header('Content-Type: application/json');
echo json_encode($lojas);
?>
