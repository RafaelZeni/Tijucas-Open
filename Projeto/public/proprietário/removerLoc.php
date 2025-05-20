<!--arquivo com a função única de remover um locatário já cadastrado-->

<?php
require '../../app/database/connection.php';

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];

    $conn = conecta_db();

    $query = "CALL pr_RemoverLocatario(?)";

    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Erro!',
            'text' => 'Erro ao preparar a consulta.',
            'redirect' => 'index.php?page=gerenciarLocatarios'
        ];
    } else {
        $stmt->bind_param("i", $empresa_id);

        if ($stmt->execute()) {
            $sweetAlert = [
                'icon' => 'success',
                'title' => 'Sucesso!',
                'text' => 'Locatário removido com sucesso!',
                'redirect' => 'index.php?page=gerenciarLocatarios'
            ];
        } else {
            $error = addslashes(htmlspecialchars($stmt->error));
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao remover o locatário: {$error}",
                'redirect' => 'index.php?page=gerenciarLocatarios'
            ];
        }

        $stmt->close();
    }

    $conn->close();
} else {
    $sweetAlert = [
        'icon' => 'error',
        'title' => 'Erro!',
        'text' => 'ID do locatário não foi identificado!',
        'redirect' => 'index.php?page=gerenciarLocatarios'
    ];
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
