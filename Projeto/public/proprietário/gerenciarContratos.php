<!--Página responsável por gerenciar os contratos dos locatários.
Ele exibe uma tabela com os contratos existentes e permite ao locatário 
visualizar as informações de cada contrato, assim como permite ao proprietário
editar ou excluir os contratos já existentes-->


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Gerenciar Contratos</title>
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
        <a href="index.php?page=gerenciarLocatarios" class="<?= ($_GET['page'] == 'gerenciarLocatarios') ? 'ativo' : ''; ?>">Gerenciar Locatários</a>
        <a href="index.php?page=gerenciarContratos" class="<?= ($_GET['page'] == 'gerenciarContratos') ? 'ativo' : ''; ?>">Gerenciar Contratos</a>
        <a href="index.php?page=gerenciarLojas" class="<?= ($_GET['page'] == 'gerenciarLojas') ? 'ativo' : ''; ?>">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos" class="<?= ($_GET['page'] == 'gerenciarEspacos') ? 'ativo' : ''; ?>">Gerenciar Espaços</a>
      </nav>

    <div class="logout">
      <a href="../logout.php"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <h1>Gerenciar contratos</h1>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <a href="index.php?page=gerarContratos" class="btn btn-primary mb-3">Criar Contrato</a>
          <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
          <h2>Contratos Ativos</h2>
          <div class="table-wrapper">
            <table class="table table-striped-green text-center">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome da Empresa</th>
                  <th>Espaço Locado</th>
                  <th>Data do Início</th>
                  <th>Data do Término</th>
                  <th>Valor Mensal</th>
                  <th>Responsável</th>
                  <th>Status</th>
                  <th>Desativar</th>
                </tr>
              </thead>
              <tbody>
                <?php
                require '../../app/database/connection.php';
                $conn = conecta_db();
                $query = "SELECT c.contrato_id, c.espaco_id, l.empresa_nome, c.data_inicio, c.nome_loc, c.valor_mensal, c.contrato_status FROM tb_contrato c JOIN tb_locatarios l ON c.empresa_id = l.empresa_id WHERE c.contrato_status = 'Ativo'";

                $resultado = $conn->query($query);

                while ($linha = $resultado->fetch_object()) {
                  //faz com que apareça a data de fim, como definida no contrato, 12 meses após a data inicial
                  $data_inicio_obj = new DateTime($linha->data_inicio);
                  $data_inicio_formatada = $data_inicio_obj->format('d-m-Y');

                  $data_fim_obj = clone $data_inicio_obj;
                  $data_fim = $data_fim_obj->add(new DateInterval('P12M'))->format('d-m-Y');

                  echo "<tr>";
                  echo "<td>{$linha->contrato_id}</td>";
                  echo "<td>{$linha->empresa_nome}</td>";
                  echo "<td>{$linha->espaco_id}</td>";
                  echo "<td>{$data_inicio_formatada}</td>";
                  echo "<td>{$data_fim}</td>";
                  echo "<td>{$linha->valor_mensal}</td>";
                  echo "<td>{$linha->nome_loc}</td>";
                  echo "<td>{$linha->contrato_status}</td>";

                  echo "<td>
                              <a class='btn btn-danger btn-excluir' href='index.php?page=removerContrato&id={$linha->contrato_id}' data-text='Deseja desativar o contrato da empresa {$linha->empresa_nome}?'>
                                <img src='../conteudo_livre/assets/imgs/lixeira.png' alt='Desativar'>
                              </a>
                            </td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>






          <h2 style="margin-top: 10px;">Contratos Invativos</h2>
          <div class="table-wrapper">
            <table class="table table-striped-green text-center">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome da Empresa</th>
                  <th>Espaço Locado</th>
                  <th>Data do Início</th>
                  <th>Data do Término</th>
                  <th>Valor Mensal</th>
                  <th>Responsável</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = "SELECT c.contrato_id, c.espaco_id, l.empresa_nome, c.data_inicio, c.nome_loc, c.valor_mensal, c.contrato_status FROM tb_contrato c JOIN tb_locatarios l ON c.empresa_id = l.empresa_id WHERE c.contrato_status = 'Inativo'";

                $resultado = $conn->query($query);

                while ($linha = $resultado->fetch_object()) {
                  //faz com que apareça a data de fim, como definida no contrato, 12 meses após a data inicial
                  $data_inicio_obj = new DateTime($linha->data_inicio);
                  $data_inicio_formatada = $data_inicio_obj->format('d-m-Y');

                  $data_fim_obj = clone $data_inicio_obj;
                  $data_fim = $data_fim_obj->add(new DateInterval('P12M'))->format('d-m-Y');

                  echo "<tr>";
                  echo "<td>{$linha->contrato_id}</td>";
                  echo "<td>{$linha->empresa_nome}</td>";
                  echo "<td>{$linha->espaco_id}</td>";
                  echo "<td>{$data_inicio_formatada}</td>";
                  echo "<td>{$data_fim}</td>";
                  echo "<td>{$linha->valor_mensal}</td>";
                  echo "<td>{$linha->nome_loc}</td>";
                  echo "<td>{$linha->contrato_status}</td>";
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