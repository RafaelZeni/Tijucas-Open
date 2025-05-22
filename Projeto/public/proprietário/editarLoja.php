<?php
require '../../app/database/connection.php';

if (isset($_POST['loja_nome']) && isset($_POST['loja_telefone']) && isset($_POST['loja_andar']) && isset($_POST['loja_tipo'])) {
  $loja_id = $_GET['id'];
  $loja_nome = $_POST['loja_nome'];
  $loja_telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['loja_telefone']);
  $loja_andar = $_POST['loja_andar'];
  $loja_tipo = $_POST['loja_tipo'];

  $conn = conecta_db();

  $query = "CALL pr_EditarLoja(?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("issss", $loja_id, $loja_nome, $loja_telefone, $loja_andar, $loja_tipo);
  $resultado = $stmt->execute();

  if ($resultado) {
    $sweetAlert = [
      'icon' => 'success',
      'title' => 'Sucesso!',
      'text' => 'Loja editada com sucesso!',
      'redirect' => 'index.php?page=gerenciarLojas'
    ];
  } else {
    $error = addslashes(htmlspecialchars($stmt->error));
    $sweetAlert = [
      'icon' => 'error',
      'title' => 'Erro!',
      'text' => "Erro ao editar loja: {$error}",
      'redirect' => 'index.php?page=gerenciarLojas'
    ];
  }

  $stmt->close();
  $conn->close();

}

$loja_id = $_GET['id'];
$conn = conecta_db();
$query = "SELECT * FROM tb_lojas WHERE loja_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loja_id);
$stmt->execute();
$result = $stmt->get_result();
$loja = $result->fetch_object();
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
      <a href="index.php?page=gerenciarLocatarios"
        class="<?= ($_GET['page'] == 'gerenciarLocatarios') ? 'ativo' : ''; ?>">Gerenciar Locatários</a>
      <a href="index.php?page=gerenciarContratos"
        class="<?= ($_GET['page'] == 'gerenciarContratos') ? 'ativo' : ''; ?>">Gerenciar Contratos</a>
      <a href="index.php?page=gerenciarLojas"
        class="<?= ($_GET['page'] == 'gerenciarLojas') ? 'ativo' : ''; ?>">Gerenciar Lojas</a>
      <a href="index.php?page=gerenciarEspacos"
        class="<?= ($_GET['page'] == 'gerenciarEspacos') ? 'ativo' : ''; ?>">Gerenciar Espaços</a>
    </nav>

    <div class="logout">
      <a href="../logout.php"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <h2>Editando Loja - <?php echo $loja->loja_nome; ?></h2>

          <a href="index.php?page=gerenciarLojas" class="btn btn-dark mb-3">Voltar</a>
        </div>
      </div>

      <div class="row">
        <div class="col">

          <form method="POST" action="editarLoja.php?id=<?php echo $_GET['id']; ?>">
            <div class="mb-3">
              <label for="loja_nome" class="form-label">Nome da Loja:</label>
              <input type="text" name="loja_nome" id="loja_nome" class="form-control"
                value="<?php echo $loja->loja_nome; ?>" readonly>
            </div>
            <div class="mb-3">
              <label for="loja_telefone" class="form-label">Telefone:</label>
              <input type="text" name="loja_telefone" id="loja_telefone" class="form-control"
                oninput="mascararTelefone(this)" value="<?php echo $loja->loja_telefone; ?>" required>
            </div>
            <div class="mb-3">
              <label for="loja_andar" class="form-label">Andar:</label>
              <input type="text" name="loja_andar" id="loja_andar" class="form-control"
                value="<?php echo $loja->loja_andar; ?>" readonly>
            </div>
            <div class="mb-3">
              <label for="loja_tipo" class="form-label">Tipo:</label>
              <input type="text" name="loja_tipo" id="loja_tipo" class="form-control"
                value="<?php echo $loja->loja_tipo; ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php if (isset($sweetAlert)): ?>
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