<?php

include '../../app/views/include/headerProp.php';

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
    
  } else if ($_GET['page'] == 'removercontrato') {
    include 'removerContrato.php';

  } else if ($_GET['page'] == 'editarcontrato') {
    include 'editarLoc.php';

  } else if ($_GET['page'] == 'gerenciarEspacos') {
    include 'gerenciarEspacos.php';
    
  } else if ($_GET['page'] == 'editarEspaco') {
    include 'editarEspaco.php';    

  } else if ($_GET['page'] == 'acessarRelatorios') {
    include 'acessarRelatorios.php';    
    
    
  } else {
    header('location: index.php');
    
  }
  
} else {
  include 'proprietario.php';
}



?>