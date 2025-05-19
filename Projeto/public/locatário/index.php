<!--Index que leva o usuário par a página de acordo com seu clique-->

<?php


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