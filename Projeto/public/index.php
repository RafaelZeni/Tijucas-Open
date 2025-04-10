<?php

include '../app/views/include/header.php';

//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'lojas') { 
    include '../public/conteudo_livre/lojas.php';
    
  } else if ($_GET['page'] == 'gastronomia') {
    include '../public/conteudo_livre/gastronomia.php';

  } else if ($_GET['page'] == 'contato') {
    include '../public/conteudo_livre/contato.php';

  } else if ($_GET['page'] == 'entrar') {
    include '../public/conteudo_livre/login.php';


  } else {
    header('location: index.php');

  }

} else {
  include 'main.php';
}

include '../app/views/include/footer.php';


?>