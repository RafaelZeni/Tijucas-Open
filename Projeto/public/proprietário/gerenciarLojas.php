<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Gerenciar Lojas</title>
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
        <a href="index.php?page=gerenciarLocatarios">Gerenciar Locatários</a>
        <a href="index.php?page=gerenciarContratos">Gerenciar Contratos</a>
        <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
      </nav>

      <div class="logout">
        <a href="../logout.php"><span>↩</span> Log Out</a>
      </div>
    </div>

    <div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>Gerenciar Lojas</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <a href="index.php?page=cadLojas" class="btn btn-primary mb-3">Cadastrar Loja</a>
        <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
        <table class="table table-striped-green">
          <thead>
            <tr>
              <th>ID</th>
              <th>Espaço Locado</th>
              <th>Nome da Loja</th>
              <th>Telefone</th>
              <th>Andar</th>
              <th>Tipo</th>
              <th>Excluir</th>
            </tr>
          </thead>
          <tbody>
            <?php
            require '../../app/database/connection.php';
            $conn = conecta_db();
            $query = "SELECT loja_id, espaco_id, loja_nome, loja_telefone, loja_andar, loja_tipo FROM tb_lojas";

            $resultado = $conn->query($query);

            while ($linha = $resultado->fetch_object()) {
              $html = "<tr>";
              $html .= "<td>" . $linha->loja_id . "</td>";
              $html .= "<td>" . $linha->espaco_id . "</td>";
              $html .= "<td>" . $linha->loja_nome . "</td>";
              $html .= "<td>" . $linha->loja_telefone . "</td>";
              $html .= "<td>" . $linha->loja_andar . "</td>";
              $html .= "<td>" . $linha->loja_tipo . "</td>";
              $html .= "<td>
                  <a class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir essa loja?\")' href='index.php?page=removerLoja&id=" . $linha->loja_id . "'><img src='../conteudo_livre/assets/imgs/lixeira.png' alt='Excluir';'></a>
              </td>";
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
  </body>
</html>




