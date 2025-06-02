<?php
// session_start(); // Se index.php já iniciou, não precisa aqui. Se acessado diretamente, sim.
// Verifique se a sessão já foi iniciada pelo index.php ou se este arquivo pode ser acessado diretamente.
// Se puder ser acessado diretamente e depender de sessão, descomente session_start().
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8" />
    <title>Visualizar Espaços</title>
    <link rel="stylesheet" href="locatario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <button class="hamburger-menu" aria-label="Abrir menu" aria-expanded="false">
      &#9776;
    </button>

    <div class="sidebar">
        <div class="logo">
            <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
        </div>

        <?php $activePage = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
        <nav>
            <a href="index.php" class="<?= ($activePage == 'home') ? 'ativo' : ''; ?>">Início</a>
            <a href="index.php?page=visualizarEspacos" class="<?= ($activePage == 'visualizarEspacos') ? 'ativo' : ''; ?>">Visualizar Espaços</a>
            <a href="index.php?page=gestaoContratos" class="<?= ($activePage == 'gestaoContratos') ? 'ativo' : ''; ?>">Gestão de Contrato</a>
        </nav>

        <div class="logout">
             <a href="../logout.php" class="btn-confirmar" data-text="Deseja fazer logout?"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h1>Gerenciar Espaços</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                    <div class="table-wrapper">
                        <table class="table table-striped-green text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Piso</th>
                                    <th>Área(m<sup>2</sup>)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    require '../../app/database/connection.php'; // Garanta que o caminho está correto

                                    $conn = conecta_db();
                                    if ($conn) {
                                        $query = "SELECT e.espaco_id, e.espaco_piso, e.espaco_area, e.espaco_status 
                                                  FROM tb_espacos e 
                                                  LEFT JOIN tb_lojas l ON e.espaco_id = l.espaco_id";
                                    
                                        $resultado = $conn->query($query);

                                        if ($resultado) {
                                            while($linha = $resultado->fetch_object()){
                                                echo "<tr>";
                                                echo "<td data-label='ID'>" . htmlspecialchars($linha->espaco_id) . "</td>";
                                                echo "<td data-label='Piso'>" . htmlspecialchars($linha->espaco_piso) . "</td>";
                                                echo "<td data-label='Área(m²))'>" . htmlspecialchars($linha->espaco_area) . "</td>"; // Use m² para o data-label
                                                echo "<td data-label='Status'>" . htmlspecialchars($linha->espaco_status) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4'>Erro ao executar a consulta: " . htmlspecialchars($conn->error) . "</td></tr>";
                                        }
                                        $conn->close();
                                    } else {
                                        echo "<tr><td colspan='4'>Erro ao conectar ao banco de dados.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../conteudo_livre/assets/js/alerts_confirmacao.js"></script>
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