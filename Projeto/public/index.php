<?php

include '../app/views/include/header.php';

//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'lojas') { 
    include '../public/conteudo_livre/lojas.php'; //Página das Lojas
    
  } else if ($_GET['page'] == 'gastronomia') {
    include '../public/conteudo_livre/gastronomia.php'; //Página dos Restaurantes

  } else if ($_GET['page'] == 'contato') {
    include '../public/conteudo_livre/contato.php'; //Página de Contato

  } else if ($_GET['page'] == 'mapa') {
    include '../public/conteudo_livre/mapa.php'; //Página do Mapa
  
  } else if ($_GET['page'] == 'entrar') {
    include '../public/conteudo_livre/login.php'; //Página de Login

  } else if ($_GET['page'] == 'recsenha') {
    include '../public/conteudo_livre/recuperarSenha.php'; //Página de Login

  } else {
    header('location: index.php');

  }

} else {
  include 'main.php'; //Página Principal
}

include '../app/views/include/footer.php';

?>