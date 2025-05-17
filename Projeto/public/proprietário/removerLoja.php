<?php
    require '../../app/database/connection.php';

    if (isset($_GET['id'])) {
        $empresa_id = $_GET['id'];

        $conn = conecta_db();

        $query = "CALL pr_RemoverLoja(?)";

        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            echo "Erro ao preparar a consulta.";
            exit;
        }

        $stmt->bind_param("i", $empresa_id);

        if ($stmt->execute()) {
            $sweetAlert = ['icon' => 'success',
                'title' => 'Sucesso!',
                'text' => 'Loja removida com sucesso!',
                'redirect' => 'index.php?page=gerenciarLojas'];
        } else {
            $error = addslashes(htmlspecialchars($stmt->error));
            $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao remover a loja: {$error}",
                'redirect' => 'index.php?page=gerenciarLojas'];
        }

        $stmt->close();
        $conn->close();
    } else {
        $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "ID da loja não fornecido",
                'redirect' => 'index.php?page=gerenciarLojas'];
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Processando...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        const sweetAlertData = <?= json_encode($sweetAlert) ?>;
    </script>
    <script src="../conteudo_livre/assets/js/alerts.js"></script>
</body>
</html>
