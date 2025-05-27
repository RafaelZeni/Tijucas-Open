<?php
require '../../app/database/connection.php'; 

$empresa_nome = "Visitante"; 

if (isset($_SESSION['logins_id'])) {
    $conn = conecta_db();
    if ($conn) {
        $logins_id = $_SESSION['logins_id'];
        $query = "SELECT empresa_nome FROM tb_locatarios WHERE logins_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $logins_id);
            $stmt->execute();
            $stmt->bind_result($empresa_nome_db); 
            if ($stmt->fetch()) {
                $empresa_nome = $empresa_nome_db; 
            }
            $stmt->close();
        } else {
            
            error_log("Erro ao preparar a query: " . $conn->error);
        }
        $conn->close();
    } else {
       
        error_log("Erro ao conectar ao banco de dados.");
    }
} else {
   
    error_log("logins_id não encontrado na sessão."); // Apenas para depuração
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Perfil do Locatário</title>
    <link rel="stylesheet" href="locatario.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

    <button class="hamburger-menu" aria-label="Abrir menu" aria-expanded="false">
      &#9776; </button>

    <div class="sidebar">
        <div class="logo">
            <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
        </div>

        <?php
        
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        ?>

        <nav>
            <a href="index.php" class="<?= ($page == 'home' || $page == '') ? 'ativo' : ''; ?>">Início</a>
            <a href="index.php?page=visualizarEspacos" class="<?= ($page == 'visualizarEspacos') ? 'ativo' : ''; ?>">Visualizar Espaços</a>
            <a href="index.php?page=gestaoContratos" class="<?= ($page == 'gestaoContratos') ? 'ativo' : ''; ?>">Gestão de Contrato</a>
        </nav>

        <div class="logout">
            <a href="../logout.php"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <h1>Bem-Vindo <?php echo htmlspecialchars($empresa_nome); ?></h1>
        
        <?php
        /*
        if ($page == 'home') {
            // include 'conteudo_home.php';
            echo "<p>Conteúdo da página inicial.</p>";
        } elseif ($page == 'visualizarEspacos') {
            // include 'conteudo_visualizar_espacos.php';
            echo "<p>Conteúdo para visualizar espaços.</p>";
        } elseif ($page == 'gestaoContratos') {
            // include 'conteudo_gestao_contratos.php';
            echo "<p>Conteúdo para gestão de contratos.</p>";
        }
        */
        ?>
    </div>

    <script>
      const hamburgerButton = document.querySelector('.hamburger-menu');
      const sidebar = document.querySelector('.sidebar');

      if (hamburgerButton && sidebar) {
        hamburgerButton.addEventListener('click', () => {
          sidebar.classList.toggle('open');
          const isExpanded = sidebar.classList.contains('open');
          hamburgerButton.setAttribute('aria-expanded', isExpanded);
          if (isExpanded) {
            hamburgerButton.setAttribute('aria-label', 'Fechar menu');
          } else {
            hamburgerButton.setAttribute('aria-label', 'Abrir menu');
          }
        });
      }
    </script>
    </body>
</html>