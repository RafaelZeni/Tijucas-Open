<?php
require '../../app/database/connection.php';
session_start();

$conn = conecta_db();

// Verifica se o usuário é locatário e está logado
if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'locatario') {
    header('Location: login.php');
    exit;
}

// Pega o logins_id
$logins_id = $_SESSION['logins_id'];

// Descobre o empresa_id
$sqlEmpresa = "SELECT empresa_id FROM tb_locatarios WHERE logins_id = ?";
$stmtEmpresa = $conn->prepare($sqlEmpresa);
$stmtEmpresa->bind_param("i", $logins_id);
$stmtEmpresa->execute();
$resultEmpresa = $stmtEmpresa->get_result();

if ($rowEmpresa = $resultEmpresa->fetch_assoc()) {
    $empresa_id = $rowEmpresa['empresa_id'];
} else {
    echo "<script>alert('Erro ao localizar empresa.'); window.location.href='index.php';</script>";
    exit;
}

// Agora busca o contrato
$sql = "SELECT contrato_id, espaco_id, data_inicio, nome_loc
        FROM tb_contrato
        WHERE empresa_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Meu Contrato</h1>
        <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID do Contrato</th>
                    <th>ID do Espaço</th>
                    <th>Data de Início</th>
                    <th>Data de Fim</th>
                    <th>Responsável</th>
                    <th>Valor do Contrato</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($contrato = $result->fetch_assoc()): 
                    $data_inicio = new DateTime($contrato['data_inicio']);
                    $data_inicio_formatada = $data_inicio->format('d-m-Y');
                    $data_fim = $data_inicio->add(new DateInterval('P12M'))->format('d-m-Y');

                  ?>
                  
                    <tr>
                        <td><?= htmlspecialchars($contrato['contrato_id']) ?></td>
                        <td><?= htmlspecialchars($contrato['espaco_id']) ?></td>
                        <td><?= htmlspecialchars($data_inicio_formatada) ?></td>
                        <td><?= htmlspecialchars($data_fim) ?></td>
                        <td><?= htmlspecialchars($contrato['nome_loc']) ?></td>
                        <td>R$ 3000</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
