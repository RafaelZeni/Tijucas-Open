<?php
require '../../app/database/connection.php';

function validar_cnpj($cnpj) { //função para validar o CNPJ
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

function validar_senha($senha) {
    // Pelo menos 8 caracteres, 1 maiúscula, 1 número e 1 caractere especial
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica se o formulário foi enviado
    $cnpj = str_replace(['.', '/', '-'], '', $_POST['cnpjCAD']);
    $senha = $_POST['passCAD'];
    $email = $_POST['emailCAD'];

    if (!validar_cnpj($cnpj)) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'CNPJ inválido!',
            'text' => 'Por favor, insira um CNPJ válido.'
        ];
    } elseif (!validar_senha($senha)) { // Use elseif para evitar múltiplas mensagens de erro
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Senha fraca!',
            'text' => 'A senha deve ter no mínimo 8 caracteres, incluindo 1 letra maiúscula, 1 número e 1 caractere especial.'
        ];
    } else { // Se o CNPJ e a senha forem válidos, prossegue com o cadastro
        $conn = conecta_db();

        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM tb_locatarios WHERE empresa_cnpj = ?");
        $stmtCheck->bind_param("s", $cnpj);
        $stmtCheck->execute();
        $stmtCheck->bind_result($cnpjExiste);
        $stmtCheck->fetch();
        $stmtCheck->close();

        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM tb_locatarios WHERE empresa_email = ?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $stmtCheck->bind_result($emailExiste);
        $stmtCheck->fetch();
        $stmtCheck->close();



        if ($cnpjExiste > 0) { // Verifica se o CNPJ já está cadastrado
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'CNPJ já cadastrado!',
                'text' => 'Esse CNPJ já está em uso por outro locatário.'
            ];
        } else if ($emailExiste > 0) {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Email já cadastrado!',
                'text' => 'Esse email já está em uso por outro locatário.'
            ];

        } else {
            // Prossegue com o cadastro
            $nome = $_POST['nomeCAD'];
            $telefone = str_replace(['(', ')', ' ', '-'], '', $_POST['telefoneCAD']);
            
            
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

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
        }

        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Cadastro de Locatário</title>
  <link rel="stylesheet" href="proprietario.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    .form-container h2 { /* Alterado de h3 para h2 para consistência */
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
    
    /* Estilo para inputs inválidos, mantenha se desejar */
    input.is-invalid {
      border-color: red;
      background-color: #ffe6e6;
    }

    /* Ajustes para as mensagens de erro de validação */
    .text-danger {
        color: red;
        font-size: 0.9em;
        margin-bottom: 5px;
        display: none; /* Inicia oculto, será mostrado via JS */
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
        <a href="index.php?page=gerenciarContratos">Gerenciar Contratos</a>
        <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
        <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
      </nav>
    <div class="logout">
      <a href="../logout.php" class="btn-confirmar" data-text="Deseja fazer logout?"><span>↩</span> Log Out</a>
    </div>
  </div>

  <div class="content">
    <a href="index.php?page=gerenciarLocatarios" class="btn btn-dark mb-3">Voltar</a>
    
    <div class="form-container"> <h2>Cadastrar Locatário</h2> <form method="post">
        <div class="mb-3">
            <label for="nomeCAD" class="form-label">Nome da Empresa</label>
            <input type="text" id="nomeCAD" name="nomeCAD" class="form-control" required placeholder="Digite o Nome da empresa" oninput="apenasLetras(this)" />
        </div>

        <div class="mb-3">
            <label for="cnpjCAD" class="form-label">CNPJ</label>
            <div id="erroCNPJ" class="text-danger">
              CNPJ inválido! Por favor, corrija antes de enviar.
            </div>
            <input type="text" id="cnpjCAD" name="cnpjCAD" class="form-control" required placeholder="Digite o CNPJ" oninput="formatarCNPJ(this); esconderErroCNPJ();" />
        </div>

        <div class="mb-3">
            <label for="telefoneCAD" class="form-label">Telefone</label>
            <input type="text" id="telefoneCAD" name="telefoneCAD" class="form-control" required placeholder="Digite o Telefone" maxlength="15" oninput="mascararTelefone(this)" />
        </div>

        <div class="mb-3">
            <label for="emailCAD" class="form-label">Email</label>
            <input type="email" id="emailCAD" name="emailCAD" class="form-control" required placeholder="Digite o E-mail" />
        </div>

        <div class="mb-3">
            <label for="passCAD" class="form-label">Senha</label>
            <div id="erroPass" class="text-danger">
              A senha deve ter no mínimo 8 caracteres, incluindo 1 letra maiúscula, 1 número e 1 caractere especial.
            </div>
            <input type="password" id="passCAD" name="passCAD" class="form-control" required placeholder="Digite a senha" />
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>
    </div>
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

    function esconderErroCNPJ() {
      const erroCNPJ = document.getElementById("erroCNPJ");
      const cnpjInput = document.getElementById("cnpjCAD");
      erroCNPJ.style.display = "none";
      cnpjInput.classList.remove("is-invalid");
    }

    // Adicionado para ocultar a mensagem de erro da senha ao digitar
    document.getElementById('passCAD').addEventListener('input', function() {
        document.getElementById('erroPass').style.display = 'none';
        this.classList.remove('is-invalid');
    });

  </script>

<?php if(isset($sweetAlert)): ?>
  <script>
    const sweetAlertData = <?= json_encode($sweetAlert) ?>;
  </script>
<?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../conteudo_livre/assets/js/alerts.js"></script>
  <script src="../conteudo_livre/assets/js/alerts_confirmacao.js"></script>
</body>
</html>