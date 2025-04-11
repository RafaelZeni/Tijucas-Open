<?php
    require '../../app/database/connection.php';

    if (isset($_POST['empresa_nome']) && isset($_POST['empresa_telefone']) && isset($_POST['empresa_email'])) {
        $empresa_id = $_GET['id'];
        $empresa_nome = $_POST['empresa_nome'];
        $empresa_telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['empresa_telefone']);
        $empresa_email = $_POST['empresa_email'];

        $obj = conecta_db();

        $query = "CALL pr_EditarLocatario(?, ?, ?, ?)";

        $stmt = $obj->prepare($query);
        $stmt->bind_param("isss", $empresa_id, $empresa_nome, $empresa_telefone, $empresa_email);
        $resultado = $stmt->execute();

        if ($resultado) {
            header("Location: gerenciarLocatarios.php");
            exit();
        } else {
            echo "<span class='alert alert-danger'>Não foi possível editar o locatário!</span>";
            header("Location: gerenciarLocatarios.php");
        }

        $stmt->close();
        $obj->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Editar Locatário</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <h2>Editar Locatário - ID: <?php echo $_GET['id']; ?> </h2>
        <a href="gerenciarLocatarios.php" class="btn btn-dark mb-3">Voltar</a>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <?php
          $empresa_id = $_GET['id'];
          $obj = conecta_db();
          $query = "SELECT * FROM tb_locatarios WHERE empresa_id = ?";
          $stmt = $obj->prepare($query);
          $stmt->bind_param("i", $empresa_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $locatario = $result->fetch_object();
          $stmt->close();
          $obj->close();
        ?>

        <form method="POST" action="editarLoc.php?id=<?php echo $_GET['id']; ?>">
          <div class="mb-3">
            <label for="empresa_nome" class="form-label">Nome da Empresa:</label>
            <input type="text" name="empresa_nome" id="empresa_nome" class="form-control" value="<?php echo $locatario->empresa_nome; ?>" required>
          </div>
          <div class="mb-3">
            <label for="empresa_telefone" class="form-label">Telefone:</label>
            <input type="text" name="empresa_telefone" id="empresa_telefone" class="form-control" oninput="mascararTelefone(this)" value="<?php echo $locatario->empresa_telefone; ?>" required>
          </div>
          <div class="mb-3">
            <label for="empresa_email" class="form-label">Email:</label>
            <input type="email" name="empresa_email" id="empresa_email" class="form-control" value="<?php echo $locatario->empresa_email; ?>" required>
          </div>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </form>
      </div>
    </div>
  </div>
</body>

<script>
  function mascararTelefone(input) {
    let valor = input.value.replace(/\D/g, '').slice(0, 11); // só números, até 11 dígitos
    let formatado = valor;

    if (valor.length >= 1) {
      formatado = '(' + valor.substring(0, 2);
    }
    if (valor.length >= 3) {
      formatado += ') ' + valor.substring(2, valor.length >= 7 ? 7 : valor.length);
    }
    if (valor.length >= 7) {
      formatado += '-' + valor.substring(7);
    }

    input.value = formatado;
  }
</script>
</html>
