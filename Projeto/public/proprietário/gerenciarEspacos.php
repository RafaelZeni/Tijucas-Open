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
                <h1>Gerenciar Espaços</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Piso</td>
                            <td>Área(m<sup>2</sup>)</td>
                            <td>Status</td>
                            <td>Ocupado Por (Rever isso no Banco)</td>
                            <td>Editar</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            require '../../app/database/connection.php';

                            $conn = conecta_db();
                            $query = "SELECT e.espaco_id, e.espaco_piso, e.espaco_area, e.espaco_status 
                            FROM tb_espacos e 
                            LEFT JOIN tb_lojas l ON e.espaco_id = l.espaco_id";
        
                            $resultado = $conn->query($query);

                            while($linha = $resultado->fetch_object()){
                                $html = "<tr>";
                                $html .= "<td>".$linha->espaco_id."</td>";
                                $html .= "<td>".$linha->espaco_piso."</td>";
                                $html .= "<td>".$linha->espaco_area."</td>";
                                $html .= "<td>".$linha->espaco_status."</td>";
                                $html .= "<td></td>";
                                $html .= "<td><a class='btn btn-success' href='index.php?page=editarEspaco&id=".$linha->espaco_id."'><img src='../conteudo_livre/assets/imgs/editar.png' alt='Excluir' style='width: 16px; height: 16px;'></a></td>";
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