<!--Permite o locatário acessar as informações de seu contrato-->

<?php
require '../../app/database/connection.php';

$conn = conecta_db();


// Pega o logins_id
$logins_id = $_SESSION['logins_id'];

// Descobre o empresa_id
$sqlEmpresa = "SELECT empresa_id FROM tb_locatarios WHERE logins_id = ?";
$stmtEmpresa = $conn->prepare($sqlEmpresa);
$stmtEmpresa->bind_param("i", $logins_id);
$stmtEmpresa->execute();
$resultEmpresa = $stmtEmpresa->get_result();

if ($rowEmpresa = $resultEmpresa->fetch_assoc()) {
  $empresa_id = $rowEmpresa['empresa_id'];
} else {
  echo "<script>alert('Erro ao localizar empresa.'); window.location.href='index.php';</script>";
  exit;
}

// Agora busca o contrato
$sql = "SELECT contrato_id, espaco_id, data_inicio, nome_loc, valor_mensal
        FROM tb_contrato
        WHERE empresa_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Gerenciar Contrato</title>
  <link rel="stylesheet" href="locatario.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="sidebar">
    <div class="logo">
      <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
    </div>

    <nav>
      <a href="index.php">Início</a>
      <a href="index.php?page=visualizarEspacos" class="<?= ($_GET['page'] == 'visualizarEspacos') ? 'ativo' : ''; ?>">Visualizar Espaços</a>
      <a href="index.php?page=gestaoContratos" class="<?= ($_GET['page'] == 'gestaoContratos') ? 'ativo' : ''; ?>">Gestão de Contrato</a>
    </nav>

    <div class="logout">
      <a href="../logout.php"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <h1>Meu Contrato</h1>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
          <div class="table-wrapper">
            <table class="table table-striped-green text-center">
              <thead>
                <tr>
                  <th>ID do Contrato</th>
                  <th>ID do Espaço</th>
                  <th>Data de Início</th>
                  <th>Data de Fim</th>
                  <th>Responsável</th>
                  <th>Valor do Contrato</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($contrato = $result->fetch_assoc()):
                  $data_inicio_obj = new DateTime($contrato['data_inicio']);
                  $data_inicio_formatada = $data_inicio_obj->format('d-m-Y');

                  $data_fim_obj = clone $data_inicio_obj;
                  $data_fim = $data_fim_obj->add(new DateInterval('P12M'))->format('d-m-Y');
                  $valor_formatado = number_format($contrato['valor_mensal'], 2, ',', '.');

                  ?>

                  <tr>
                    <td><?= htmlspecialchars($contrato['contrato_id']) ?></td>
                    <td><?= htmlspecialchars($contrato['espaco_id']) ?></td>
                    <td><?= htmlspecialchars($data_inicio_formatada) ?></td>
                    <td><?= htmlspecialchars($data_fim) ?></td>
                    <td><?= htmlspecialchars($contrato['nome_loc']) ?></td>
                    <td>R$<?= htmlspecialchars($valor_formatado) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>