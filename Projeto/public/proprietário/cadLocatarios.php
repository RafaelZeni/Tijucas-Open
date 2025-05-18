<?php
require '../../app/database/connection.php';

function validar_cnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) return false;

    $multiplicadores1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $multiplicadores2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    $numeros = substr($cnpj, 0, 12);
    $digitos = substr($cnpj, 12);

    $soma = 0;
    for ($i = 0; $i < 12; $i++) $soma += $numeros[$i] * $multiplicadores1[$i];
    $digito1 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);
    if ($digito1 != $digitos[0]) return false;

    $numeros .= $digito1;
    $soma = 0;
    for ($i = 0; $i < 13; $i++) $soma += $numeros[$i] * $multiplicadores2[$i];
    $digito2 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);

    return $digito2 == $digitos[1];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj = str_replace(['.', '/', '-'], '', $_POST['cnpjCAD']);
    
    if (!validar_cnpj($cnpj)) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'CNPJ inválido!',
            'text' => 'Por favor, insira um CNPJ válido.'
        ];
    } else {
        $nome     = $_POST['nomeCAD'];
        $telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['telefoneCAD']);
        $email    = $_POST['emailCAD'];
        $senha    = $_POST['passCAD'];
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $conn = conecta_db();
        $stmt = $conn->prepare("CALL pr_AdicionarLocatario(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $cnpj, $nome, $telefone, $email, $senhaHash);

        if ($stmt->execute()) {
            $sweetAlert = [
                'icon' => 'success',
                'title' => 'Pronto!',
                'text' => 'Locatário cadastrado com sucesso!',
                'redirect' => 'index.php?page=gerenciarLocatarios'
            ];
        } else {
            $erro = addslashes(htmlspecialchars($conn->error));
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao cadastrar locatário: {$erro}"
            ];
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Perfil do Proprietário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="proprietario.css" />
  <link rel="stylesheet" href="assets/css/cadloc.css" />
  <style>
    input.is-invalid {
      border-color: red;
      background-color: #ffe6e6;
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
      <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
      <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
    </nav>
    <div class="logout">
      <a href="../logout.php"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <section>
      <div class="quadrado">
        <section class="form-container">
          <h3>Cadastrar Locatário</h3>
          <a href="index.php?page=gerenciarLocatarios" class="btn btn-dark mb-3">Voltar</a>
          <form method="post" onsubmit="return validarFormulario();">
            <label for="nomeCAD">Nome</label>
            <input type="text" id="nomeCAD" name="nomeCAD" required placeholder="Digite o Nome da empresa" oninput="apenasLetras(this)" />

            <label for="cnpjCAD">CNPJ</label>
            <div id="erroCNPJ" style="color: red; display: none; font-size: 0.9em; margin-bottom: 5px;">
              CNPJ inválido! Por favor, corrija antes de enviar.
            </div>
            <input type="text" id="cnpjCAD" name="cnpjCAD" required placeholder="Digite o CNPJ" oninput="formatarCNPJ(this); esconderErroCNPJ();" />

            <label for="telefoneCAD">Telefone</label>
            <input type="text" id="telefoneCAD" name="telefoneCAD" required placeholder="Digite o Telefone" maxlength="15" oninput="mascararTelefone(this)" />

            <label for="emailCAD">Email</label>
            <input type="email" id="emailCAD" name="emailCAD" required placeholder="Digite o E-mail" />

            <label for="passCAD">Senha</label>
            <input type="password" id="passCAD" name="passCAD" required placeholder="Digite a senha" />

            <button type="submit" class="enviar">Cadastrar</button>
          </form>
        </section>
      </div>
    </section>
  </div>

  <script>
    function formatarCNPJ(input) {
      let valor = input.value.replace(/\D/g, "").slice(0, 14);
      let formatado = valor;
      if (valor.length > 2) formatado = valor.slice(0, 2) + "." + valor.slice(2);
      if (valor.length > 5) formatado = formatado.slice(0, 6) + "." + formatado.slice(6);
      if (valor.length > 8) formatado = formatado.slice(0, 10) + "/" + formatado.slice(10);
      if (valor.length > 12) formatado = formatado.slice(0, 15) + "-" + formatado.slice(15);
      input.value = formatado;
    }

    function mascararTelefone(input) {
      let valor = input.value.replace(/\D/g, "").slice(0, 11);
      let formatado = valor;
      if (valor.length >= 1) formatado = "(" + valor.substring(0, 2);
      if (valor.length >= 3) formatado += ") " + valor.substring(2, valor.length >= 7 ? 7 : valor.length);
      if (valor.length >= 7) formatado += "-" + valor.substring(7);
      input.value = formatado;
    }

    function apenasLetras(campo) {
      campo.value = campo.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
    }

    function validarCNPJ(cnpj) {
      cnpj = cnpj.replace(/[^\d]+/g, "");
      if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
      let tamanho = cnpj.length - 2;
      let numeros = cnpj.substring(0, tamanho);
      let digitos = cnpj.substring(tamanho);
      let soma = 0;
      let pos = tamanho - 7;
      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      if (resultado != digitos.charAt(0)) return false;
      tamanho += 1;
      numeros = cnpj.substring(0, tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      return resultado == digitos.charAt(1);
    }

    function validarFormulario() {
      const cnpjInput = document.getElementById("cnpjCAD");
      const erroCNPJ = document.getElementById("erroCNPJ");
      if (!validarCNPJ(cnpjInput.value)) {
        erroCNPJ.style.display = "block";
        cnpjInput.classList.add("is-invalid");
        cnpjInput.focus();
        return false;
      }
      erroCNPJ.style.display = "none";
      cnpjInput.classList.remove("is-invalid");
      return true;
    }

    function esconderErroCNPJ() {
      const erroCNPJ = document.getElementById("erroCNPJ");
      const cnpjInput = document.getElementById("cnpjCAD");
      erroCNPJ.style.display = "none";
      cnpjInput.classList.remove("is-invalid");
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
