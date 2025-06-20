<!--Página que permite ao proprietário visualilzar quais 
espaços ocupados, por quem eles estão ocupados, a área do 
espaço em metros quadrados, o ID do espaço e o andar em que 
ele se encontra -->

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Gerenciar Espaços</title>
  <link rel="stylesheet" href="proprietario.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="sidebar">
    <div class="logo">
      <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
    </div>

    <nav>
      <a href="index.php">Início</a>
      <a href="index.php?page=gerenciarLocatarios"
        class="<?= ($_GET['page'] == 'gerenciarLocatarios') ? 'ativo' : ''; ?>">Gerenciar Locatários</a>
      <a href="index.php?page=gerenciarContratos"
        class="<?= ($_GET['page'] == 'gerenciarContratos') ? 'ativo' : ''; ?>">Gerenciar Contratos</a>
      <a href="index.php?page=gerenciarLojas"
        class="<?= ($_GET['page'] == 'gerenciarLojas') ? 'ativo' : ''; ?>">Gerenciar Lojas</a>
      <a href="index.php?page=gerenciarEspacos"
        class="<?= ($_GET['page'] == 'gerenciarEspacos') ? 'ativo' : ''; ?>">Gerenciar Espaços</a>
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
                  <th>Empresa</th>
                </tr>
              </thead>
              <tbody>
                <?php
                require '../../app/database/connection.php';

                $conn = conecta_db();
                $query = "SELECT e.espaco_id, e.espaco_piso, e.espaco_area, e.espaco_status, l.empresa_nome
                                        FROM tb_espacos e 
                                        LEFT JOIN tb_contrato c ON e.espaco_id = c.espaco_id AND c.contrato_status = 'Ativo'
                                        LEFT JOIN tb_locatarios l ON c.empresa_id = l.empresa_id";


                $resultado = $conn->query($query);

                if ($resultado->num_rows > 0) {

                  while ($linha = $resultado->fetch_object()) {
                    $html = "<tr>";
                    $html .= "<td>" . $linha->espaco_id . "</td>";
                    $html .= "<td>" . $linha->espaco_piso . "</td>";
                    $html .= "<td>" . $linha->espaco_area . "</td>";
                    $html .= "<td>" . $linha->espaco_status . "</td>";
                    $html .= "<td>" . ($linha->empresa_nome ?? '') . "</td>";
                    $html .= "</tr>";
                    echo $html;
                  }
                } else {
                  echo "<tr><td colspan='5'>Nenhum espaço encontrado.</td></tr>";
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
</body>

</html>