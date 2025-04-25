<?php
session_start();

if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'proprietario') {
    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página do Locatário</title>
  <link rel="stylesheet" href="assets/css/locatario.css">
</head>

<body>

    <header class="header">
        <a href="#"> <img src="../conteudo_livre/assets/imgs/logo.jpeg" alt="logo TIJUCAS" class="logo-tijucas"></a>
        <div class="user-section">
            <button class="user-button">Locatário</button>  
            <a href="#"><img src="assets/imgs/usu.png" alt="UsuarioTijucasOpen" class="user-icon"></a>
        </div>
    </header>

    

    <div class="container">
        <div class="button-container">
            <a href="index.php?page=visualizarEspacos" class="action-button">
                <span>Visualizar Espaços</span>
            </a>
            <a href="index.php?page=gestaoContratos" class="action-button">
                <span>Gestão de Contratos</span>
            </a>
        </div>
    </div>

</body>
</html>