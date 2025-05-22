<!--Página que permite a edição de certas informaçõs sobre o locatário, como o nome da empresa, o telefone e o e-mail-->

<?php
    require '../../app/database/connection.php';

    if (isset($_POST['empresa_nome']) && isset($_POST['empresa_telefone']) && isset($_POST['empresa_email'])) {
        $empresa_id = $_GET['id'];
        $empresa_nome = $_POST['empresa_nome'];
        $empresa_telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['empresa_telefone']);
        $empresa_email = $_POST['empresa_email'];

        $conn = conecta_db();

        $query = "CALL pr_EditarLocatario(?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $empresa_id, $empresa_nome, $empresa_telefone, $empresa_email);
        $resultado = $stmt->execute();

        if ($resultado) {
            $sweetAlert = ['icon' => 'success',
                'title' => 'Sucesso!',
                'text' => 'Locatário editado com sucesso!',
                'redirect' => 'index.php?page=gerenciarLocatarios'];
        } else {
            $error = addslashes(htmlspecialchars($stmt->error));
            $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao editar o locatário: {$error}",
                'redirect' => 'index.php?page=gerenciarLocatarios'];
        }

        $stmt->close();
        $conn->close();
    }
?>

<?php
          $empresa_id = $_GET['id'];
          $conn = conecta_db();
          $query = "SELECT * FROM tb_locatarios WHERE empresa_id = ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("i", $empresa_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $locatario = $result->fetch_object();
          $stmt->close();
          $conn->close();
        ?>



<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Perfil do Proprietário</title>
    <link rel="stylesheet" href="proprietario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <div class="sidebar">
      <div class="logo">
        <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
      </div>

      <nav>
        <a href="index.php">Início</a>
        <a href="index.php?page=gerenciarLocatarios" class="<?= ($_GET['page'] == 'gerenciarLocatarios') ? 'ativo' : ''; ?>">Gerenciar Locatários</a>
        <a href="index.php?page=gerenciarContratos" class="<?= ($_GET['page'] == 'gerenciarContratos') ? 'ativo' : ''; ?>">Gerenciar Contratos</a>
        <a href="index.php?page=gerenciarLojas" class="<?= ($_GET['page'] == 'gerenciarLojas') ? 'ativo' : ''; ?>">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos" class="<?= ($_GET['page'] == 'gerenciarEspacos') ? 'ativo' : ''; ?>">Gerenciar Espaços</a>
      </nav>
        
      <div class="logout">
        <a href="../logout.php"><span>↩</span> Log Out</a>
      </div>
    </div>

    <div class="content">
    <div class="container-fluid">
    <div class="row">
      <div class="col">
        <h2>Editando Locatário - <?php echo $locatario->empresa_nome; ?></h2>

        <a href="index.php?page=gerenciarLocatarios" class="btn btn-dark mb-3">Voltar</a>
      </div>
    </div>

    <div class="row">
      <div class="col">

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
    </div>
    <?php if(isset($sweetAlert)): ?>
  <script>
    const sweetAlertData = <?= json_encode($sweetAlert) ?>;
  </script>
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../conteudo_livre/assets/js/alerts.js"></script>
  </body>
  <script>
  function mascararTelefone(input) {
    let valor = input.value.replace(/\D/g, '').slice(0, 11); // só números, até 11 dígitos
    let formatado = valor;

    if (valor.length >= 2) {
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