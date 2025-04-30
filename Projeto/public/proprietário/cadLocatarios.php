<?php
require '../../app/database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj = str_replace(['.', '/', '-'], '', $_POST['cnpjCAD']);
    $nome     = $_POST['nomeCAD'];
    $telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['telefoneCAD']);
    $email    = $_POST['emailCAD'];
    $senha    = $_POST['passCAD'];

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $conn = conecta_db();

    $stmt = $conn->prepare("CALL pr_AdicionarLocatario(?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $cnpj, $nome, $telefone, $email, $senhaHash);

    if ($stmt->execute()) {
        echo "<script>alert('Locatário cadastrado com sucesso!'); window.location.href = 'gerenciarLocatarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $conn->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Perfil do Proprietário</title>
    <link rel="stylesheet" href="proprietario.css">
    <link rel="stylesheet" href="assets/css/cadloc.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
        <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
      </nav>

      <div class="logout">
        <a href="../logout.php"><span>↩</span> Log Out</a>
      </div>
    </div>

    <div class="content">
    <section class="">
    <div class="quadrado">
        <section class="form-container">
            <h3>Cadastrar Locatário</h3>
            <a href="index.php?page=gerenciarLocatarios" class="btn btn-dark mb-3">Voltar</a>
            <form method="post">
                <label for="nomeCAD">Nome</label>
                <input type="text" id="nomeCAD" name="nomeCAD" required placeholder="Digite o Nome da empresa" oninput="apenasLetras(this)">

                <label for="cnpjCAD">CNPJ</label>
                <input type="text" id="cnpjCAD" name="cnpjCAD" required placeholder="Digite o CNPJ" oninput="formatarCNPJ(this)">

                <label for="telefoneCAD">Telefone</label>
                <input type="text" id="telefoneCAD" name="telefoneCAD" required placeholder="Digite o Telefone" maxlength="15" oninput="mascararTelefone(this)">

                <label for="emailCAD">Email</label>
                <input type="email" id="emailCAD" name="emailCAD" required placeholder="Digite o E-mail">

                <label for="passCAD">Senha</label>
                <input type="password" id="passCAD" name="passCAD" required placeholder="Digite a senha">

                <button type="submit" class="enviar">Enviar</button>
            </form>
        </section>
    </div> 
</section>
<script>
  function formatarCNPJ(input) {
    let valor = input.value.replace(/\D/g, ''); 

    if (valor.length > 14) valor = valor.slice(0, 14); 

    let formatado = valor;

    if (valor.length > 2) {
      formatado = valor.slice(0, 2) + '.' + valor.slice(2);
    }
    if (valor.length > 5) {
      formatado = formatado.slice(0, 6) + '.' + formatado.slice(6);
    }
    if (valor.length > 8) {
      formatado = formatado.slice(0, 10) + '/' + formatado.slice(10);
    }
    if (valor.length > 12) {
      formatado = formatado.slice(0, 15) + '-' + formatado.slice(15);
    }

    input.value = formatado;
  }
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
  function apenasLetras(campo) {
    campo.value = campo.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
  }
</script>
    </div>
  </body>
</html>




