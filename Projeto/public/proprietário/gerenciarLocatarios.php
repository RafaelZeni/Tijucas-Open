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
                <a href="cadLocatarios.php">Cadastrar Locatário</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Nome da Empresa</th>
                            <th>CNPJ</th>
                            <th>Email</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            include 'Projeto\app\database\connection.php';
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>

</html>