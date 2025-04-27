<?php


//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'gerenciarLocatarios') {
    include '../../app/views/include/headerProp.php';
    include 'gerenciarLocatarios.php';
    
  } else if ($_GET['page'] == 'cadLocatarios') {
    include 'cadLocatarios.php';
    
  } else if ($_GET['page'] == 'removerlocatario') {
    include '../../app/views/include/headerProp.php';
    include 'removerLoc.php';
    
  } else if ($_GET['page'] == 'editarlocatario') {
    include '../../app/views/include/headerProp.php';
    include 'editarLoc.php';
    
  } else if ($_GET['page'] == 'gerenciarContratos') {
    include '../../app/views/include/headerProp.php';
    include 'gerenciarContratos.php';
    
  } else if ($_GET['page'] == 'gerarContratos') {
    include '../../app/views/include/headerProp.php';
    include 'gerarContratos.php';
    
  } else if ($_GET['page'] == 'removerContrato') {
    include '../../app/views/include/headerProp.php';
    include 'removerContrato.php';

  } else if ($_GET['page'] == 'editarcontrato') {
    include '../../app/views/include/headerProp.php';
    include 'editarLoc.php';

  } else if ($_GET['page'] == 'gerenciarEspacos') {
    include '../../app/views/include/headerProp.php';
    include 'gerenciarEspacos.php';
    
  } else if ($_GET['page'] == 'editarEspaco') {
    include '../../app/views/include/headerProp.php';
    include 'editarEspaco.php';
    
  } else if ($_GET['page'] == 'cadLojas') {
    include '../../app/views/include/headerProp.php';
    include 'cadLojas.php'; 

  } else if ($_GET['page'] == 'acessarRelatorios') {
    include '../../app/views/include/headerProp.php';
    include 'acessarRelatorios.php';    
    
    
  } else {
    include '../../app/views/include/headerProp.php';
    header('location: index.php');
    
  }
  
} else {
  include '../../app/views/include/headerProp.php';
  include 'proprietario.php';
}



?>