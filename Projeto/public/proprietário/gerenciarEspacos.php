<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Espaços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>Gerenciar Espaços</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <a href="index.php?page=cadastrarEspacos" class="btn btn-primary mb-3">Cadastrar Espaço</a>
                <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                <table class="table">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>ID</td>
                            <td>Piso</td>
                            <td>Área</td>
                            <td>Status</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            require '../../app/database/connection.php';

                            $obj = conecta_db();
                            $query = "SELECT espaco_id, espaco_piso, espaco_area, espaco_status FROM tb_espacos";
                            $resultado = $obj->query($query);

                            while($linha = $resultado->fetch_object()){
                                $html = "<tr>";
                                $html .= "<td><a class='btn btn-danger' href='index.php?page=removerEspaco&id=".$linha->espaco_id."'>Excluir</a>";
                                $html .= "<a class='btn btn-success' href='index.php?page=editarEspaco&id=".$linha->espaco_id."'>Editar</a></td>";
                                $html .= "<td>".$linha->espaco_id."</td>";
                                $html .= "<td>".$linha->espaco_piso."</td>";
                                $html .= "<td>".$linha->espaco_area."</td>";
                                $html .= "<td>".$linha->espaco_status."</td>";
                                $html .= "<tr>";
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