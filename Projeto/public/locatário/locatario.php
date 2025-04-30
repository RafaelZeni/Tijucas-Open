<?php
session_start();

if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'locatario') {
    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
    exit();
}
require '../../app/database/connection.php';
$conn = conecta_db();

$logins_id = $_SESSION['logins_id'];
$query = "SELECT empresa_nome FROM tb_locatarios WHERE logins_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logins_id);
$stmt->execute();
$stmt->bind_result($empresa_nome);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Perfil do Locatário</title>
    <link rel="stylesheet" href="locatario.css">
  </head>
  <body>
    <div class="sidebar">
      <div class="logo">
        <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
      </div>

      <nav>
        <a href="index.php">Início</a>
        <a href="index.php?page=visualizarEspacos">Visualizar Espaços</a>
        <a href="index.php?page=gestaoContratos">Gestão de Contrato</a>
      </nav>

      <div class="logout">
        <a href="../logout.php"><span>↩</span> Log Out</a>
      </div>
    </div>

    <div class="content">
      <h1>Bem-Vindo <?php echo htmlspecialchars($empresa_nome); ?></h1>
    </div>
  </body>
</html>