<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Contratos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>ID</th>
              <th>Nome da Empresa</th>
              <th>Data do Início</th>
              <th>Data do Fim</th>
              <th>Contrato</th>
            </tr>
          </thead>
          <tbody>
            <?php
            require '../../app/database/connection.php';
            $obj = conecta_db();
            $query = "SELECT c.contrato_id, l.empresa_nome, c.data_inicio, c.data_fim, c.pdf_contrato FROM tb_contrato c JOIN tb_locatarios l ON c.empresa_id = l.empresa_id";

            $resultado = $obj->query($query);

            while ($linha = $resultado->fetch_object()) {
              $html = "<tr>";
              $html .= "<td>
                                        <a class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir esse contrato?\")' href='index.php?page=removerContrato&id=" . $linha->contrato_id . "'>Excluir</a>
                                        <a class='btn btn-success' onclick='return confirm(\"Editar locatários?\")' href='index.php?page=editarContrato&id=" . $linha->contrato_id . "'>Editar</a>
                                    </td>";
              $html .= "<td>" . $linha->empresa_nome . "</td>";
              $html .= "<td>" . $linha->data_inicio . "</td>";
              $html .= "<td>" . $linha->data_fim . "</td>";
              $html .= "<td>" . $linha->pdf_contrato . "</td>";
              $html .= "</tr>";
              echo $html;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>