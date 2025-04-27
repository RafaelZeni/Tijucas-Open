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
  <title>Perfil do Proprietário</title>
  <link rel="stylesheet" href="./assets/css/proprietario.css">

</head>
<body>

<div class="container-btns">
  <table>
    <tbody>
      <tr>
        <td><a href="index.php?page=gerenciarLocatarios" class="btn-prop">Gerenciar Locatários</a></td>
        <td><a href="index.php?page=gerenciarContratos" class="btn-prop">Gerenciar Contratos</a></td>
      </tr>
      <tr>
        <td><a href="index.php?page=gerenciarEspacos" class="btn-prop">Gerenciar Espaços</a></td>
        <td><a href="index.php?page=acessarRelatorios" class="btn-prop">Acessar Relatórios</a></td>
      </tr>
      <tr>
        <td colspan="2"><a href="index.php?page=cadLojas" class="btn-prop">Cadastrar Lojas</a></td>
      </tr>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
