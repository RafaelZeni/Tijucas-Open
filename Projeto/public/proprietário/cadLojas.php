<?php
require '../../app/database/connection.php';
$conn = conecta_db();

// Se for envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../conteudo_livre/assets/imgs/';
    $uniqueName = uniqid() . '_' . basename($_FILES['loja_logo']['name']);
    $destPath = $uploadDir . $uniqueName;
    $fullPath = __DIR__ . '/' . $destPath;

    if (move_uploaded_file($_FILES['loja_logo']['tmp_name'], $fullPath)) {
        $logoPath = 'conteudo_livre/assets/imgs/' . $uniqueName;
    } else {
        echo "<script>alert('Erro ao mover arquivo de imagem'); window.location.href = 'index.php?page=cadLojas';</script>";
        exit;
    }

    $nome = $_POST['loja_nome'];
    $telefone = $_POST['loja_telefone'];
    $andar = $_POST['loja_andar'];
    $tipo = $_POST['loja_tipo'];
    $espaco_id = $_POST['espaco_id'];

    $sql = "CALL pr_CriarLoja(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $espaco_id, $nome, $telefone, $logoPath, $andar, $tipo);

    if ($stmt->execute()) {
        echo "<script>alert('Loja cadastrada com sucesso!'); window.location.href = 'index.php';</script>";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }
    $stmt->close();
}

// Puxa contratos ativos (empresas + espaços)
// Puxa contratos ativos (empresas + espaços) que não têm loja associada
$query = "
    SELECT 
        c.contrato_id, 
        l.empresa_nome, 
        c.espaco_id, 
        e.espaco_piso
    FROM tb_contrato c
    INNER JOIN tb_locatarios l ON c.empresa_id = l.empresa_id
    INNER JOIN tb_espacos e ON c.espaco_id = e.espaco_id
    LEFT JOIN tb_lojas lo ON c.espaco_id = lo.espaco_id
    WHERE lo.espaco_id IS NULL"; // Apenas empresas que não têm loja
$result = $conn->query($query);

$contratos = [];
while ($row = $result->fetch_assoc()) {
    $contratos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Loja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/cadlojas.css">
    <script>
    function preencherEspacoEPiso() {
        const selectEmpresa = document.getElementById('empresa_select');
        const selectedOption = selectEmpresa.options[selectEmpresa.selectedIndex];
        const espacoId = selectedOption.getAttribute('data-espaco');
        const piso = selectedOption.getAttribute('data-piso');

        document.querySelector('[name="espaco_id"]').value = espacoId;
        document.querySelector('[name="loja_andar"]').value = piso;
    }
    </script>
</head>
<body>
    <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
    <form action="cadLojas.php" method="POST" enctype="multipart/form-data">
        
        <label>Empresa (Locatário):</label>
        <select id="empresa_select" name="loja_nome" onchange="preencherEspacoEPiso()" required>
            <option value="">Selecione a empresa</option>
            <?php foreach ($contratos as $contrato): ?>
                <option 
                    value="<?= htmlspecialchars($contrato['empresa_nome']) ?>" 
                    data-espaco="<?= $contrato['espaco_id'] ?>" 
                    data-piso="<?= $contrato['espaco_piso'] ?>">
                    <?= htmlspecialchars($contrato['empresa_nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Telefone da Loja:</label>
        <input type="text" name="loja_telefone"><br>

        <label>Andar:</label>
        <input type="text" name="loja_andar" readonly required><br>

        <label>Espaço:</label>
        <input type="text" name="espaco_id" readonly required><br>

        <label>Tipo da Loja:</label>
        <select name="loja_tipo" required>
            <option value="Alimentação">Restaurante</option>
            <option value="Roupas">Roupas</option>
            <option value="Esportes">Esportes</option>
            <option value="Livros">Livros</option>
        </select><br>

        <label>Logo da Loja:</label>
        <input type="file" name="loja_logo" required><br>

        <input type="submit" value="Cadastrar Loja">
    </form>
</body>
</html>
