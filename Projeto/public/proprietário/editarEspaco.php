<?php
require '../../app/database/connection.php';

if (isset($_POST['espaco_piso']) && isset($_POST['espaco_area']) && isset($_POST['espaco_status'])) {
    $espaco_id = $_GET['id'];
    $espaco_piso = $_POST['espaco_piso'];
    $espaco_area = $_POST['espaco_area'];
    $espaco_status = $_POST['espaco_status'];

    $obj = conecta_db();

    $query = "CALL pr_editarEspaco(?, ?)";

    $stmt = $obj->prepare($query);
    $stmt->bind_param("is", $espaco_id, $espaco_area);
    $resultado = $stmt->execute();

    if ($resultado) {
        header("location: gerenciarEspacos.php");
        exit();
    } else {
        echo "<span class='alert alert-danger'>Não foi possível editar o espaço!</span>";
        header('location: gerenciarEspacos.php');
    }
    $stmt->close();
    $obj->close();

}

$espaco_id = $_GET['id'];
$obj = conecta_db();
$query = "SELECT * FROM tb_espacos WHERE espaco_id = ?";
$stmt = $obj->prepare($query);
$stmt->bind_param("i", $espaco_id);
$stmt->execute();
$resultado = $stmt->get_result();
$espaco = $resultado->fetch_object();
$stmt->close();
$obj->close();

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
                <h2>Editando Espaço - <?php echo $espaco->espaco_id ?></h2>
                <a href="index.php?page=gerenciarEspacos" class="btn btn-dark mb-3">Voltar</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form method="post" action="index.php?page=editarEspaco&id=<?php echo $_GET['id']; ?>">
                    <div class="mb-3">
                        <label for="espaco_id" class="form-label">ID do Espaço:</label>
                        <input type="text" name="espaco_id" id="espaco_id" class="form-control"
                            value="<?php echo $espaco->espaco_id ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="espaco_piso" class="form-label">Piso:</label>
                        <input type="text" name="espaco_piso" id="espaco_piso" class="form-control"
                            value="<?php echo $espaco->espaco_piso; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="espaco_area" class="form-label">Área(m<sup>2</sup>):</label>
                        <input type="text" name="espaco_area" id="espaco_area" class="form-control"
                            value="<?php echo $espaco->espaco_area; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="espaco_status" class="form-label">Status:</label>
                        <input type="text" name="espaco_status" id="espaco_status" class="form-control"
                            value="<?php echo $espaco->espaco_status; ?>" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </form>
            </div>
        </div>

</body>

</html>