<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Tijucas Open</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./conteudo_livre/assets/css/main.css">
</head>
<body>

<header class="header">
  <div class="trio">

  </div>
  <div class="trio">
    <nav class="botoesMenu">
      <?php
        $page = $_GET['page'] ?? 'inicio';
      ?>
    <ul>
      <li class="<?= ($page == 'inicio') ? 'ativo' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/index.php"> <h2>Início</h2></a>
      </li>
      <li class="<?= ($page == 'lojas') ? 'ativo' : '' ?>">
        <a href="index.php?page=lojas"><h2>Lojas</h2></a>
      </li>
      <li class="<?= ($page == 'mapa') ? 'ativo' : '' ?>">
        <a href="index.php?page=mapa"><h2>Mapa-interativo</h2></a>
      </li>
    </ul>
  </nav>
  </div>
  <div class="trio">
    <div class="divBotaoEntrar">
      <a href="index.php?page=entrar"><button class="btn-entrar">Entrar</button></a>
    </div>
  </div>
</header>

</body>
</html>