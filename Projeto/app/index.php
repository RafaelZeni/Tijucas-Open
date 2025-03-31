<?php
include 'header.php';
include 'conexao.php';

//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 1) { 
    include 'add.php';
    
  } else if ($_GET['page'] == 2) {
    include 'delete.php';

  } else if ($_GET['page'] == 3) {
    include 'edit.php';

  } else {
    header('location: index.php');

  }

} else {
  include 'main.php';
}

?>