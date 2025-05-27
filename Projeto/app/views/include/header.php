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
    <ul>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/main.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/index.php"> <h2>Início</h2></a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/lojas.php') ? 'destaque' : '' ?>">
        <a href="index.php?page=lojas"><h2>Lojas</h2></a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/mapa.php') ? 'destaque' : '' ?>">
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