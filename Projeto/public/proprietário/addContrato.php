<?php
/* ANÁLISE NECESSÁRIA */


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar se todos os campos do formulário foram enviados
    $nome   = isset($_POST['nome']) ? $_POST['nome'] : '';
    $cnpj   = isset($_POST['cnpj']) ? $_POST['cnpj'] : '';
    $loja   = isset($_POST['loja']) ? $_POST['loja'] : '';
    $espaco = isset($_POST['espaço']) ? $_POST['espaço'] : '';  // Usando isset para evitar "undefined array key"
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';

    $data = isset($_POST['data']) ? DateTime::createFromFormat('Y-m-d', $_POST['data']) : null;
    $dataFormatada = $data ? $data->format('Y-m-d') : '';

    if (empty($nome) || empty($cnpj) || empty($modelo) || empty($dataFormatada)) {
        echo "Por favor, preencha todos os campos obrigatórios!";
        exit;
    }

    require '../../app/database/connection.php';
    $conn = conecta_db(); // Conexão com mysqli

    // Buscar o ID da empresa pelo CNPJ
    $stmt = $conn->prepare("SELECT empresa_id FROM tb_locatarios WHERE empresa_cnpj = ?");
    $stmt->bind_param("s", $cnpj);
    $stmt->execute();
    $stmt->bind_result($empresa_id);
    $stmt->fetch(); 
    $stmt->close();

    if ($empresa_id) {
        // Inserir na tabela tb_contrato
        $insert = $conn->prepare("INSERT INTO tb_contrato (empresa_id, data_inicio, nome_loc) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $empresa_id, $dataFormatada, $nome);
        $insert->execute();
        $insert->close();

        // Gerar o PDF
        
    } else {
        echo "<script>alert('CNPJ não encontrado na tabela de locatários!'); window.location.href = 'index.php?page=gerarContratos';</script>";
    }
}
?>