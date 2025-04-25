<?php
require '../../app/database/connection.php';
$conn = conecta_db(); // agora $conn estará disponível para tudo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../conteudo_livre/assets/imgs/';
    $uniqueName = uniqid() . '_' . basename($_FILES['loja_logo']['name']);
    $destPath = $uploadDir . $uniqueName;
    $fullPath = __DIR__ . '/' . $destPath;

    if (move_uploaded_file($_FILES['loja_logo']['tmp_name'], $fullPath)) {
        $logoPath = 'conteudo_livre/assets/imgs/' . $uniqueName;
    } else {
        echo "<script>alert('Erro ao mover arquivo de imagem'); window.location.href = 'cadLojas.php';</script>";
    }

    $nome = $_POST['loja_nome'];
    $telefone = $_POST['loja_telefone'];
    $andar = $_POST['loja_andar'];
    $tipo = $_POST['loja_tipo'];
    $espaco_id = $_POST['espaco_id'];

    $sql = "INSERT INTO tb_lojas (espaco_id, loja_nome, loja_telefone, loja_logo, loja_andar, loja_tipo)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $espaco_id, $nome, $telefone, $logoPath, $andar, $tipo);

    if ($stmt->execute()) {
        $conn->query("UPDATE tb_espacos SET espaco_status = 'Alugado' WHERE espaco_id = $espaco_id");
        echo "<script>alert('Loja cadastrada com sucesso!'); window.location.href = 'index.php';</script>";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}

// Carregar todos os espaços disponíveis
$espacos = $conn->query("SELECT espaco_id FROM tb_espacos WHERE espaco_status = 'Disponível'");
$espacos_disponiveis = [];
while ($row = $espacos->fetch_assoc()) {
    $espacos_disponiveis[] = $row['espaco_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Loja</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/cadlojas.css">
  <script>
    const espacosDisponiveis = <?= json_encode($espacos_disponiveis) ?>;

    function filtrarEspacosPorAndar() {
      const andarSelecionado = document.querySelector('[name="loja_andar"]').value;
      const selectEspacos = document.querySelector('[name="espaco_id"]');
      selectEspacos.innerHTML = '<option value="">Selecione um espaço</option>';

      const faixa = andarSelecionado === 'L1' ? [1, 12] : [13, 24];

      espacosDisponiveis.forEach(id => {
        if (id >= faixa[0] && id <= faixa[1]) {
          const opt = document.createElement('option');
          opt.value = id;
          opt.textContent = id;
          selectEspacos.appendChild(opt);
        }
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector('[name="loja_andar"]').addEventListener('change', filtrarEspacosPorAndar);
    });
  </script>
</head>
<body>
  <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
  <form action="cadLojas.php" method="POST" enctype="multipart/form-data">
    <label>Nome da Loja:</label>
    <input type="text" name="loja_nome" required><br>

    <label>Telefone:</label>
    <input type="text" name="loja_telefone"><br>

    <label>Andar:</label>
    <select name="loja_andar" required>
        <option value="">Selecione um andar</option>
        <option value="L1">L1</option>
        <option value="L2">L2</option>
    </select><br>

    <label>Espaço:</label>
    <select name="espaco_id" required>
      <option value="">Selecione um espaço</option>
      <!-- Preenchido dinamicamente com JavaScript -->
    </select><br>

    <label>Tipo:</label>
    <select name="loja_tipo">
        <option value="restaurante">Restaurante</option>
        <option value="roupas">Roupas</option>
        <option value="informatica">Informática</option>
        <option value="esportes">Esportes</option>
    </select><br>

    <label>Imagem (logo):</label>
    <input type="file" name="loja_logo" required><br>

    <input type="submit" value="Cadastrar Loja">
  </form>
</body>
</html>
