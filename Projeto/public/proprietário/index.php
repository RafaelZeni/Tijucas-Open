<?php


//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'gerenciarLocatarios') { 
    include 'gerenciarLocatarios.php';
    
  } else if ($_GET['page'] == 'gerenciarContratos') {
    include 'gerenciarContratos.php';

  } else if ($_GET['page'] == 'acessarRelatorios') {
    include 'acessarRelatorios.php';
    

  } else {
    header('location: index.php');

  }

} else {
  include 'proprietario.php';
}



?>