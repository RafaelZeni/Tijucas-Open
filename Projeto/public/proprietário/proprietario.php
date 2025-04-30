<?php
session_start();

if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'proprietario') {
    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Perfil do Proprietário</title>
    <link rel="stylesheet" href="proprietario.css">
  </head>
  <body>
    <div class="sidebar">
      <div class="logo">
        <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
      </div>

      <nav>
        <a href="index.php?page=gerenciarLocatarios">Gerenciar Locatários</a>
        <a href="index.php?page=gerenciarContratos">Gerenciar Contratos</a>
        <a href="index.php?page=cadLojas">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
      </nav>

      <div class="logout">
        <a href="../logout.php"><span>↩</span> Log Out</a>
      </div>
    </div>

    <div class="content">
      <h1>Bem-Vindo Cristiano</h1>
    </div>
  </body>
</html>