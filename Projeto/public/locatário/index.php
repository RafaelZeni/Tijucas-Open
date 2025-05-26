<!--Index que leva o usuário par a página de acordo com seu clique-->

<?php
session_start();
// Verifica se o usuário é locatário e está logado
if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'locatario') { // Verifica se o usuário é locatário e está logado
    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
    exit();
}


//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'gestaoContratos') { //Página de gestão de contratos
    include 'gestaoContratos.php';
    
  } else if ($_GET['page'] == 'visualizarEspacos') { //Página de visualizar espaços
    include 'visualizarEspacos.php';

  } else {
    header('location: index.php'); // Redireciona para a página inicial se a página não for reconhecida

  }

} else {
  include 'locatario.php';
}



?>