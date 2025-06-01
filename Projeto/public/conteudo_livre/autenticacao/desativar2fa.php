<?php

require '../../app/database/connection.php';

$conn = conecta_db();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logins_id'])) {
    header("Location: ../../public/login.php");
    exit();
}

$logins_id = $_SESSION['logins_id'];
$sweetAlert = null; // Inicializa sweetAlert como nulo

$query = "SELECT auth_secret FROM tb_logins WHERE logins_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logins_id);
$stmt->execute();
$result = $stmt->get_result();
$linha = $result->fetch_assoc();

// Verifica se auth_secret EXISTE (ou seja, 2FA está ativa)
if (!empty($linha['auth_secret'])) {
    // Se existe, então desativa (seta para NULL)
    $stmt_update = $conn->prepare("UPDATE tb_logins SET auth_secret = NULL WHERE logins_id = ?");
    $stmt_update->bind_param("i", $logins_id); // Não precisa de 's' para setar NULL
    $stmt_update->execute();
    $stmt_update->close();

    $sweetAlert = [
        'icon' => 'success',
        'title' => 'Sucesso!',
        'text' => 'Sua autenticação de dois fatores foi desativada com sucesso.'
    ];

} else {
    // Se não existe (está vazio ou NULL), então a 2FA não está ativa
    $sweetAlert = [
        'icon' => 'info', // Melhor usar 'info' ou 'warning'
        'title' => 'Atenção!',
        'text' => 'Sua autenticação de dois fatores já está inativa.'
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desativar 2FA</title>
</head>
<body>

<?php if (isset($sweetAlert)): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const sweetAlertData = <?= json_encode($sweetAlert) ?>;
        Swal.fire({
            icon: sweetAlertData.icon,
            title: sweetAlertData.title,
            text: sweetAlertData.text,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = 'index.php';
            }
        });
    </script>
<?php endif; ?>

</body>
</html>