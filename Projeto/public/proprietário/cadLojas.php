<!--Página que permite o Proprietário cadastrar uma loja, desde que já 
tenha um proprietário com um contrato ativo-->

<?php
require '../../app/database/connection.php';
$conn = conecta_db();

// Se for envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../conteudo_livre/assets/imgs/';
    $uniqueName = uniqid() . '_' . basename($_FILES['loja_logo']['name']);
    $destPath = $uploadDir . $uniqueName;
    $fullPath = __DIR__ . '/' . $destPath;

    if (move_uploaded_file($_FILES['loja_logo']['tmp_name'], $fullPath)) {
        $logoPath = 'conteudo_livre/assets/imgs/' . $uniqueName;
    } else {
        echo "<script>alert('Erro ao mover arquivo de imagem'); window.location.href = 'index.php?page=cadLojas';</script>";
        exit;
    }

    $nome = $_POST['loja_nome'];
    $telefone = $_POST['loja_telefone'];
    $andar = $_POST['loja_andar'];
    $tipo = $_POST['loja_tipo'];
    $espaco_id = $_POST['espaco_id'];

    $sql = "CALL pr_CriarLoja(?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $espaco_id, $nome, $telefone, $logoPath, $andar, $tipo);

    if ($stmt->execute()) {
        $sweetAlert = ['icon' => 'success',
                'title' => 'Sucesso!',
                'text' => 'Loja cadastrada com sucesso!',
                'redirect' => 'index.php?page=gerenciarLojas'];
    } else {
       $error = addslashes(htmlspecialchars($stmt->error));
       $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao cadastrar loja: {$error}"];
    }
    $stmt->close();
}

// Puxa contratos ativos (empresas + espaços)
// Puxa contratos ativos (empresas + espaços) que não têm loja associada
$query = "
    SELECT 
        c.contrato_id, 
        l.empresa_nome, 
        c.espaco_id, 
        e.espaco_piso
    FROM tb_contrato c
    INNER JOIN tb_locatarios l ON c.empresa_id = l.empresa_id
    INNER JOIN tb_espacos e ON c.espaco_id = e.espaco_id
    LEFT JOIN tb_lojas lo ON c.espaco_id = lo.espaco_id
    WHERE lo.espaco_id IS NULL"; // Apenas empresas que não têm loja
$result = $conn->query($query);

$contratos = [];
while ($row = $result->fetch_assoc()) {
    $contratos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Perfil do Proprietário</title>
    <link rel="stylesheet" href="proprietario.css">
    <link rel="stylesheet" href="./assets/css/cadlojas.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function preencherEspacoEPiso() {
        const selectEmpresa = document.getElementById('empresa_select');
        const selectedOption = selectEmpresa.options[selectEmpresa.selectedIndex];
        const espacoId = selectedOption.getAttribute('data-espaco');
        const piso = selectedOption.getAttribute('data-piso');

        document.querySelector('[name="espaco_id"]').value = espacoId;
        document.querySelector('[name="loja_andar"]').value = piso;
    }
    </script>
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
    <a href="index.php?page=gerenciarLojas" class="btn btn-dark mb-3">Voltar</a>
    <form action="cadLojas.php" method="POST" enctype="multipart/form-data">
        
        <label>Empresa (Locatário):</label>
        <select id="empresa_select" name="loja_nome" onchange="preencherEspacoEPiso()" required>
            <option value="">Selecione a empresa</option>
            <?php foreach ($contratos as $contrato): ?>
                <option 
                    value="<?= htmlspecialchars($contrato['empresa_nome']) ?>" 
                    data-espaco="<?= $contrato['espaco_id'] ?>" 
                    data-piso="<?= $contrato['espaco_piso'] ?>">
                    <?= htmlspecialchars($contrato['empresa_nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Telefone da Loja:</label>
        <input type="text" name="loja_telefone" required placeholder="Digite o Telefone" maxlength="15" oninput="mascararTelefone(this)"><br>

        <label>Andar:</label>
        <input type="text" name="loja_andar" readonly required><br>

        <label>Espaço:</label>
        <input type="text" name="espaco_id" readonly required><br>

        <label>Tipo da Loja:</label>
        <select name="loja_tipo" required>
            <option value="Alimentação">Restaurante</option>
            <option value="Roupas">Roupas</option>
            <option value="Esportes">Esportes</option>
            <option value="Livros">Livros</option>
            <option value="Jóias">Jóias</option>
        </select><br>

        <label>Logo da Loja:</label>
        <input type="file" name="loja_logo" required><br>

        <input type="submit" class="btn accept mb-3" value="Cadastrar Loja">
    </form>
    </div>


  <script>
    function mascararTelefone(input) {
      let valor = input.value.replace(/\D/g, "").slice(0, 11);
      let formatado = valor;
      if (valor.length >= 1) formatado = "(" + valor.substring(0, 2);
      if (valor.length >= 3) formatado += ") " + valor.substring(2, valor.length >= 7 ? 7 : valor.length);
      if (valor.length >= 7) formatado += "-" + valor.substring(7);
      input.value = formatado;
    }
  </script>


  <?php if(isset($sweetAlert)): ?>
    <script>
    const sweetAlertData = <?= json_encode($sweetAlert) ?>;
    </script>
  <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../conteudo_livre/assets/js/alerts.js"></script>
  </body>
</html>