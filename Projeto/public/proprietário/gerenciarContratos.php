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
              <th>Espaço Locado</th>
              <th>Data do Início</th>
              <th>Data do Término</th>
            </tr>
          </thead>
          <tbody>
            <?php
            require '../../app/database/connection.php';
            $obj = conecta_db();
            $query = "SELECT c.contrato_id, c.espaco_id, l.empresa_nome, c.data_inicio, c.nome_loc FROM tb_contrato c JOIN tb_locatarios l ON c.empresa_id = l.empresa_id";

            $resultado = $obj->query($query);

            while ($linha = $resultado->fetch_object()) {
              //Rafael: faz com que apareça a data de fim, como definida no contrato, 12 meses após a data inicial
              $data_inicio = new DateTime($linha->data_inicio);
              $data_inicio_formatada = $data_inicio->format('d-m-Y');

              $data_fim = $data_inicio->add(new DateInterval('P12M'))->format('d-m-Y');


              $html = "<tr>";
              $html .= "<td>
<<<<<<< HEAD
                                        <a class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir esse contrato?\")' href='index.php?page=removerContrato&id=" . $linha->contrato_id . "'>Excluir</a>
                                        <a class='btn btn-success' onclick='return confirm(\"Editar locatários?\")' href='index.php?page=editarContrato&id=" . $linha->contrato_id . "'>Editar</a>
                                    </td>";
=======
                          <a class='btn btn-danger' href='index.php?page=removerContrato&id=" . $linha->contrato_id . "' onclick=\"return confirm('Tem certeza que deseja excluir este contrato?')\">Excluir</a>
                          <a class='btn btn-success' href='index.php?page=editarContrato&id=" . $linha->contrato_id . "'>Editar</a>
                      </td>";
              $html .= "<td>" . $linha->contrato_id . "</td>";
>>>>>>> c22030358ab7652b920e08953c62a2bf4e02a738
              $html .= "<td>" . $linha->empresa_nome . "</td>";
              $html .= "<td>" . $linha->espaco_id . "</td>";
              $html .= "<td>" . $data_inicio_formatada . "</td>";
              $html .= "<td>" . $data_fim . "</td>";
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