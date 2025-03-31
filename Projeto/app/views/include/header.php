<?php
  function base_url()
  {
    return dirname($_SERVER['SCRIPT_FILENAME']);
  }

  // Obtém a URL atual
  $current_page = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Tijucas Open</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/Projeto/public/conteudo_livre/assets/css/main.css">
</head>
<body>

<header>
  <div class="logo">
    <img src='/Tijucas-Open/Projeto/public/conteudo_livre/assets/imgs/logo.jpeg' alt="Logo Tijucas Open">
  </div>
  <nav>
    <ul>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/main.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/main.php">Início</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/lojas.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/conteudo_livre/lojas.php">Lojas</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/gastronomia.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/conteudo_livre/gastronomia.php">Gastronomia</a>
      </li>
      <li class="<?= ($current_page == '/Tijucas-Open/Projeto/public/conteudo_livre/contato.php') ? 'destaque' : '' ?>">
        <a href="/Tijucas-Open/Projeto/public/conteudo_livre/contato.php">Fale Conosco</a>
      </li>
    </ul>
  </nav>
  <a href="../página login/index.html"><button class="btn-entrar">Entrar</button></a>
</header>

</body>
</html>
