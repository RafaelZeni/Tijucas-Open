<?php
require '../../app/database/connection.php';
$conn = conecta_db(); // agora $conn estará disponível para tudo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // continua igual...
    $uploadDir = '../conteudo_livre/assets/imgs/';
    $uniqueName = uniqid() . '_' . basename($_FILES['loja_logo']['name']);
    $destPath = $uploadDir . $uniqueName;
    $fullPath = __DIR__ . '/' . $destPath;

    if (move_uploaded_file($_FILES['loja_logo']['tmp_name'], $fullPath)) {
      $logoPath = 'conteudo_livre/assets/imgs/' . $uniqueName;
    } else {
        echo "Erro ao mover o arquivo!";
        exit;
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
        echo "Loja cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./assets/css/cadlojas.css">
</head>
<body>
  <form action="cadLojas.php" method="POST" enctype="multipart/form-data">
    <label>Nome da Loja:</label>
    <input type="text" name="loja_nome" required><br>

    <label>Telefone:</label>
    <input type="text" name="loja_telefone"><br>

    <label>Andar:</label>
    <select name="loja_andar">
        <option value="L1">L1</option>
        <option value="L2">L2</option>
    </select><br>

    <?php

      $espacos = $conn->query("SELECT espaco_id FROM tb_espacos WHERE espaco_status = 'Disponível'");
    ?>

    <select name="espaco_id" required>
    <option value="">Selecione um espaço</option>
    <?php while ($row = $espacos->fetch_assoc()): ?>
      <option value="<?= $row['espaco_id'] ?>"><?= $row['espaco_id'] ?></option>
    <?php endwhile; ?>
    </select>


    <label>Tipo:</label>
    <select name="loja_tipo">
        <option value="restaurante">Restaurante</option>
        <option value="roupas">Roupas</option>
        <option value="informatica">Informática</option>
        <option value="esportes">Esportes</option>
    </select><br>

    <label>Imagem (logo):</label>
    <input type="file" name="loja_logo"><br>

    <input type="submit" value="Cadastrar Loja">
  </form>

  
</body>
</html>