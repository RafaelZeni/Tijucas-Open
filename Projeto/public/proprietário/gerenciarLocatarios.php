<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gerenciarLocatarios</title>
    <link rel="stylesheet" href="conteudo_livre/assets/css/gerenciarLocatarios.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nome da Empresa</th>
                            <th>CNPJ</th>
                            <th>Email</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            require '../../app/database/connection.php';
                            $obj = conecta_db();
                            $query = "SELECT l.empresa_id, l.empresa_nome, l.empresa_cnpj, l.empresa_email, l.empresa_telefone, lg.email_usu 
                                    FROM tb_locatarios l
                                    JOIN tb_logins lg ON l.logins_id = lg.logins_id";

                            $resultado = $obj->query($query);

                            while ($linha = $resultado->fetch_object()) {
                                $html = "<tr>";
                                $html .= "<td>
                                          <a class='btn btn-danger' href='index.php?page=removerlocatario&id=" . $linha->empresa_id . "'>Excluir</a>
                                          <a class='btn btn-success' href='index.php?page=editarlocatario&id=" . $linha->empresa_id . "'>Editar</a>
                                          </td>";
                                $html .= "<td>" . $linha->empresa_id . "</td>";
                                $html .= "<td>" . $linha->empresa_nome . "</td>";
                                $html .= "<td>" . $linha->empresa_cnpj . "</td>";
                                $html .= "<td>" . $linha->empresa_email . "</td>";
                                $html .= "<td>" . $linha->empresa_telefone . "</td>";
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