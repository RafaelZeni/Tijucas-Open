<?php
require '../../app/database/connection.php'; 

$empresa_nome = "Visitante"; 

if (isset($_SESSION['logins_id'])) {
    $conn = conecta_db();
    if ($conn) {
        $logins_id = $_SESSION['logins_id'];
        $query = "SELECT empresa_id, empresa_nome FROM tb_locatarios WHERE logins_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $logins_id);
            $stmt->execute();
            $stmt->bind_result($empresa_id_db,$empresa_nome_db); 
            if ($stmt->fetch()) {
                $empresa_id = $empresa_id_db;
                $empresa_nome = $empresa_nome_db; 
            }
            $stmt->close();
        } else {
            
            error_log("Erro ao preparar a query: " . $conn->error);
        }
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
        <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>Gerenciar Boletos</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <a href="index.php?page=ativar2fa" class="btn btn-primary mb-3">Ativar 2FA</a>
                <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                <div class="table-wrapper">
                  <table class="table table-striped-green text-center">
                      <thead>
                          <tr>
                              <th>ID</th>
                              <th>Contrato</th>
                              <th>Número do Boleto</th>
                              <th>Valor</th>
                              <th>Vencimento</th>
                              <th>Banco</th>
                              <th>Codigo de Barras</th>
                              <th>Gerar</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                              $query = "SELECT b.boleto_id, b.contrato_id, b.numero_documento, b.valor, b.vencimento, b.banco, b.linha_digitavel
                                        FROM tb_boletos b JOIN tb_contrato c ON b.contrato_id = c.contrato_id WHERE c.empresa_id = $empresa_id";

          
                              $resultado = $conn->query($query);

                              while($boleto = $resultado->fetch_object()){
                                  $html = "<tr>";
                                  $html .= "<td>".$boleto->boleto_id."</td>";
                                  $html .= "<td>".$boleto->contrato_id."</td>";
                                  $html .= "<td>".$boleto->numero_documento."</td>";
                                  $html .= "<td>".$boleto->valor."</td>";
                                  $html .= "<td>".$boleto->vencimento."</td>";
                                  $html .= "<td>".$boleto->banco."</td>";
                                  $html .= "<td>".$boleto->linha_digitavel."</td>";
                                  $html .= "<td><a class='btn btn-primary' href='index.php?page=gerarBoletoLoc&id={$boleto->boleto_id}'>Gerar</a></td>";
                                  $html .= "</tr>";
                                  echo $html;
                              }
                          ?>
                      </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
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