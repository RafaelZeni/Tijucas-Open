<?php

include '../app/views/include/header.php';

//ORGANIZAR DIRECIONAMENTOS DAS PÁGINAS

if(isset($_GET['page'])) {
  if ($_GET['page'] == 'gestaoContratos') { 
    include 'gestaoContratos.php';
    
  } else if ($_GET['page'] == 'visualizarEspacos') {
    include 'visualizarEspacos.php';

  } else {
    header('location: index.php');

  }

} else {
  include 'locatario.php';
}

include '../app/views/include/footer.php';


?>