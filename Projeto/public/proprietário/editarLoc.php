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
  <title>Editar Locatário</title>
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
        <a href="index.php?page=gerenciarLocatarios" class="ativo">Gerenciar Locatários</a>
        <a href="index.php?page=gerenciarContratos" >Gerenciar Contratos</a>
        <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
      </nav>
        
    <div class="logout">
       <a href="../logout.php" class="btn-confirmar" data-text="Deseja fazer logout?"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <a href="index.php?page=gerenciarLocatarios" class="btn btn-dark mb-3">Voltar</a>
    
    <div class="form-container">
      <h2>Editando Locatário - <?php echo $locatario->empresa_nome; ?></h2>

      <form method="POST" action="editarLoc.php?id=<?php echo $_GET['id']; ?>">
        <div class="mb-3">
          <label for="empresa_nome" class="form-label">Nome da Empresa:</label>
          <input type="text" name="empresa_nome" id="empresa_nome" class="form-control" value="<?php echo htmlspecialchars($locatario->empresa_nome); ?>" required oninput="apenasLetras(this)">
        </div>
        <div class="mb-3">
          <label for="empresa_telefone" class="form-label">Telefone:</label>
          <input type="text" name="empresa_telefone" id="empresa_telefone" class="form-control" oninput="mascararTelefone(this)" value="<?php echo htmlspecialchars($locatario->empresa_telefone); ?>" required>
        </div>
        <div class="mb-3">
          <label for="empresa_email" class="form-label">Email:</label>
          <input type="email" name="empresa_email" id="empresa_email" class="form-control" value="<?php echo htmlspecialchars($locatario->empresa_email); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Salvar alterações</button>
      </form>
    </div>
  </div>

  <?php if(isset($sweetAlert)): ?>
  <script>
    const sweetAlertData = <?= json_encode($sweetAlert) ?>;
  </script>
  <?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../conteudo_livre/assets/js/alerts.js"></script>
  <script src="../conteudo_livre/assets/js/alerts_confirmacao.js"></script>
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

  function apenasLetras(campo) {
      campo.value = campo.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
  }
  </script>
</body>
</html>