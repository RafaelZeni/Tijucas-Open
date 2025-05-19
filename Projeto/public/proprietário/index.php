<!--Index que leva o usuário para a página de acordo com seu clique-->

<?php

//include '../../app/views/include/headerProp.php';
//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'gerenciarLocatarios') {
    include 'gerenciarLocatarios.php';
    
  } else if ($_GET['page'] == 'cadLocatarios') {
    include 'cadLocatarios.php';
    
  } else if ($_GET['page'] == 'removerlocatario') {
    include 'removerLoc.php';
    
  } else if ($_GET['page'] == 'editarlocatario') {
    include 'editarLoc.php';
    
  } else if ($_GET['page'] == 'gerenciarContratos') {
    include 'gerenciarContratos.php';
    
  } else if ($_GET['page'] == 'gerarContratos') {
    include 'gerarContratos.php';
    
  } else if ($_GET['page'] == 'removerContrato') {
    include 'removerContrato.php';

  } else if ($_GET['page'] == 'editarcontrato') {
    include 'editarLoc.php';

  } else if ($_GET['page'] == 'gerenciarEspacos') {
    include 'gerenciarEspacos.php';
    
  } else if ($_GET['page'] == 'gerenciarLojas') {
    include 'gerenciarLojas.php'; 

  } else if ($_GET['page'] == 'projecao') {
    include 'projecao.php'; 

  } else if ($_GET['page'] == 'cadLojas') {
    include 'cadLojas.php'; 

  } else if ($GET['page'] == 'editarLojas'){
    include 'editarLojas.php';
    
  } else if ($_GET['page'] == 'removerLoja') {
    include 'removerLoja.php';
  } else {
    include '../../app/views/include/headerProp.php';
    header('location: index.php');
    
  }
  
} else {
  include 'proprietario.php';
}



?>