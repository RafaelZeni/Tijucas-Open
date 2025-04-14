<?php
    if(isset($_POST['espaco_piso']) && isset($_POST['espaco_area']) && isset($_POST['espaco_status'])){
        $espaco_id = $_GET['id'];
        $espaco_piso = $_POST['espaco_piso'];
        $espaco_piso = $_POST['espaco_area'];
        $espaco_piso = $_POST['espaco_status'];

        $obj = conecta_db();

        $query = "CALL pr_editarEspaco(?, ?, ?)";

        $stmt = $obj->prepare($query);
        $stmt->bind_param("iss", $espaco_id, $espaco_piso, $espaco_status);
        $resultado = $stmt->execute();

        if($resultado){
            header("location: gerenciarEspacos.php");
            exit();
        } else {
            echo "<span class='alert alert";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Espaço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h2>Editando Espaço - <?php echo $espaco_id ?></h2>
            </div>
        </div>
    </div>
    
</body>
</html>