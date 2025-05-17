<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Gerenciar Locatário</title>
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
                <h1>Gerenciar Locatários</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <a href="index.php?page=cadLocatarios" class="btn btn-primary mb-3">Cadastrar Locatário</a>
                <a href="index.php" class="btn btn-dark mb-3">Voltar</a>

                <div class="table-wrapper">
                  <table class="table table-striped-green">
                      <thead>
                          <tr>
                              <th>ID</th>
                              <th>Nome da Empresa</th>
                              <th>CNPJ</th>
                              <th>Email</th>
                              <th>Telefone</th>
                              <th>Editar</th>
                              <th>Excluir</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                              require '../../app/database/connection.php';
                              $conn = conecta_db();
                              $query = "SELECT l.empresa_id, l.empresa_nome, l.empresa_cnpj, l.empresa_email, l.empresa_telefone, lg.email_usu 
                                      FROM tb_locatarios l
                                      JOIN tb_logins lg ON l.logins_id = lg.logins_id";

                              $resultado = $conn->query($query);

                              while ($linha = $resultado->fetch_object()) {
                                  $html = "<tr>";
                                  $html .= "<td>" . $linha->empresa_id . "</td>";
                                  $html .= "<td>" . $linha->empresa_nome . "</td>";
                                  $html .= "<td>" . $linha->empresa_cnpj . "</td>";
                                  $html .= "<td>" . $linha->empresa_email . "</td>";
                                  $html .= "<td>" . $linha->empresa_telefone . "</td>";
                                  $html .= "<td>
                                            <a class='btn btn-success' href='index.php?page=editarlocatario&id=" . $linha->empresa_id . "'><img src='../conteudo_livre/assets/imgs/editar.png' alt='Editar';'></a>
                                            </td>";
                                  $html .= "<td>
                                            <a class='btn btn-danger btn-excluir' href='index.php?page=removerlocatario&id=" . $linha->empresa_id . "'><img src='../conteudo_livre/assets/imgs/lixeira.png' alt='Excluir';'></a>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../conteudo_livre/assets/js/alerts_confirmacao.js"></script>
  </body>
</html>





