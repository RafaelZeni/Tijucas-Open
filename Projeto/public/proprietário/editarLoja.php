<?php
require '../../app/database/connection.php';

if (isset($_POST['loja_nome']) && isset($_POST['loja_telefone']) && isset($_POST['loja_andar']) && isset($_POST['loja_tipo'])) {
    $loja_id = $_GET['id'];
    $loja_nome = $_POST['loja_nome'];
    $loja_telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['loja_telefone']);

    $conn = conecta_db();

    $query = "CALL pr_EditarLoja(?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $loja_id, $loja_nome, $loja_telefone);
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
  <title>Editar Loja</title>
  <link rel="stylesheet" href="proprietario.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Estilos específicos para o conteúdo e formulário */
    .content {
        margin-left: 250px; /* Margem esquerda para compensar a sidebar */
        padding: 20px;
        flex-grow: 1; /* Ocupa o restante do espaço horizontal */
        display: flex; /* Para centralizar o conteúdo do formulário */
        flex-direction: column;
        align-items: center; /* Centraliza horizontalmente o conteúdo */
        justify-content: flex-start; /* Alinha o conteúdo ao topo do container */
        min-height: 100vh; /* Garante que o content ocupe pelo menos a altura da viewport */
    }

    .form-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 100%; /* Ajuste a largura do formulário */
        max-width: 600px; /* Largura máxima do formulário para evitar que fique muito largo */
        margin-top: 20px; /* Espaço acima do formulário */
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    .form-control,
    .form-select {
        border-radius: 5px;
        width: 100%; /* Garante que os inputs e selects ocupem toda a largura disponível */
        padding: 8px 12px;
        margin-bottom: 15px; /* Espaço entre os campos */
        border: 1px solid #ced4da;
    }

    .form-label {
        display: block; /* Garante que o label ocupe sua própria linha */
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    .btn-primary {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        font-size: 1.1em;
        background-color: #385c30; /* Cor do botão primário */
        border-color: #385c30;
        color: white; /* Cor do texto do botão */
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2e4b26;
        border-color: #2e4b26;
    }

    .btn-dark {
        margin-bottom: 20px; /* Espaço abaixo do botão "Voltar" */
        align-self: flex-start; /* Alinha o botão "Voltar" à esquerda do content */
    }
  </style>
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
      <a href="index.php?page=gerenciarLojas" class="ativo">Gerenciar Lojas</a>
      <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
    </nav>

    <div class="logout">
      <a href="../logout.php"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <a href="index.php?page=gerenciarLojas" class="btn btn-dark mb-3">Voltar</a>
    
    <div class="form-container">
      <h2>Editando Loja - <?php echo htmlspecialchars($loja->loja_nome); ?></h2>

      <form method="POST" action="editarLoja.php?id=<?php echo htmlspecialchars($_GET['id']); ?>">
        <div class="mb-3">
          <label for="loja_nome" class="form-label">Nome da Loja:</label>
          <input type="text" name="loja_nome" id="loja_nome" class="form-control"
            value="<?php echo htmlspecialchars($loja->loja_nome); ?>">
        </div>
        <div class="mb-3">
          <label for="loja_telefone" class="form-label">Telefone:</label>
          <input type="text" name="loja_telefone" id="loja_telefone" class="form-control"
            oninput="mascararTelefone(this)" value="<?php echo htmlspecialchars($loja->loja_telefone); ?>" required>
        </div>
        <div class="mb-3">
          <label for="loja_andar" class="form-label">Andar:</label>
          <input type="text" name="loja_andar" id="loja_andar" class="form-control"
            value="<?php echo htmlspecialchars($loja->loja_andar); ?>" readonly>
        </div>
        <div class="mb-3">
          <label for="loja_tipo" class="form-label">Tipo:</label>
          <input type="text" name="loja_tipo" id="loja_tipo" class="form-control"
            value="<?php echo htmlspecialchars($loja->loja_tipo); ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Salvar alterações</button>
      </form>
    </div>
  </div>

  <?php if (isset($sweetAlert)): ?>
    <script>
      const sweetAlertData = <?= json_encode($sweetAlert) ?>;
    </script>
  <?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../conteudo_livre/assets/js/alerts.js"></script>
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
</body>
</html>