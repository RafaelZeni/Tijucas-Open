<!DOCTYPE html>
<html lang="en">
<head>
  <title>Tijucas Open</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./conteudo_livre/assets/css/main.css">
</head>
<body>

<header>
  <div class="logo">
    <img src='/Tijucas-Open/Projeto/public/conteudo_livre/assets/imgs/logo.jpeg' alt="Logo Tijucas Open">
  </div>
  <nav>
    <ul>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/main.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/index.php">Início</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/lojas.php') ? 'destaque' : '' ?>">
        <a href="index.php?page=lojas">Lojas</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/gastronomia.php') ? 'destaque' : '' ?>">
        <a href="index.php?page=gastronomia">Gastronomia</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/contato.php') ? 'destaque' : '' ?>">
        <a href="index.php?page=contato">Fale Conosco</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/mapa.php') ? 'destaque' : '' ?>">
        <a href="index.php?page=mapa">Mapa-interativo</a>
      </li>
    </ul>
  </nav>
  <a href="index.php?page=entrar"><button class="btn-entrar">Entrar</button></a>
</header>

</body>
</html>