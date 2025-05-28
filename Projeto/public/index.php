<!--Index que leva o usuário para a página de acordo com seu clique-->

<?php



//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'lojas') { 
    include '../app/views/include/header.php';
    include '../public/conteudo_livre/lojas.php'; //Página das Lojas
    include '../app/views/include/footer.php';

  } else if ($_GET['page'] == 'contato') {
    include '../app/views/include/header.php';
    include '../public/conteudo_livre/contato.php'; //Página de Contato
    include '../app/views/include/footer.php';

  } else if ($_GET['page'] == 'mapa') {
    include '../app/views/include/header.php';
    include '../public/conteudo_livre/mapa.php'; //Página do Mapa
  
  } else if ($_GET['page'] == 'entrar') {
    include '../public/conteudo_livre/login.php'; //Página de Login

  } else if ($_GET['page'] == 'recsenha') {
    include '../app/views/include/header.php';

    include '../public/conteudo_livre/recuperarSenha.php'; //Página de Login

  } else {
    header('location: index.php');

  }

} else {
  include '../app/views/include/header.php';
  include 'main.php'; //Página Principal
  include '../app/views/include/footer.php';
}

?>