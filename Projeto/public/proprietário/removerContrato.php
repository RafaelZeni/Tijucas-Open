<!--arquivo com a função única de remover um contrato já criado-->

<?php
/* ANÁLISE NECESSÁRIA */

    require '../../app/database/connection.php';

    if (isset($_GET['id'])){
        $contrato_id = $_GET['id'];

        $conn = conecta_db();

        $query = "CALL pr_RemoverContrato(?)";

        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo "Erro ao preparar consulta: ";
            exit;
        }

        
        $stmt->bind_param("i", $contrato_id);

        if ($stmt->execute()) {
            $sweetAlert = ['icon' => 'success',
                'title' => 'Sucesso!',
                'text' => 'Contrato removido com sucesso!',
                'redirect' => 'index.php?page=gerenciarContratos'];
        } else {
            $error = addslashes(htmlspecialchars($stmt->error));
            $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao remover contrato: {$error}",
                'redirect' => 'index.php?page=gerenciarContratos'];
        }

        $stmt->close();
        $conn->close();

    } else {
        $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "ID do Contrato não foi identificado!",
                'redirect' => 'index.php?page=gerenciarContratos'];
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