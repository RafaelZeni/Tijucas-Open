<?php
require '../../app/database/connection.php';

function validar_senha($senha) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha);
}

// Recebe email via GET - obrigatório para acessar a página
$id = $_GET['id'];

$conn = conecta_db();

// Buscar o email a partir do ID:
$sql = "SELECT empresa_email FROM tb_locatarios WHERE empresa_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $linha = $resultado->fetch_assoc();
    $email = $linha['empresa_email'];
} else {
    $email = null;
}


if (!$email) {
    // Se não tem email na URL, pode redirecionar ou mostrar erro
    header('Location: index.php?page=gerenciarLocatarios');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe dados do POST
    $novaSenha = $_POST['passREC'] ?? '';
    $confirmaSenha = $_POST['passConfirm'] ?? '';

    if ($novaSenha !== $confirmaSenha) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Erro!',
            'text' => 'As senhas não coincidem. Por favor, tente novamente.'
        ];
    } elseif (!validar_senha($novaSenha)) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Senha fraca!',
            'text' => 'A senha deve ter no mínimo 8 caracteres, incluindo 1 letra maiúscula, 1 número e 1 caractere especial.'
        ];
    } else {

        // Verifica se o email está cadastrado e não é proprietário
        $stmt = $conn->prepare("SELECT tipo_usu FROM tb_logins WHERE email_usu = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            if ($row['tipo_usu'] === 'proprietario') {
                $sweetAlert = [
                    'icon' => 'warning',
                    'title' => 'Troca de Senha Restrita',
                    'text' => 'Usuário do tipo Proprietário não pode alterar a senha.'
                ];
            } else {
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Chama procedure para atualizar senha (recebe email e senha)
                $stmtUpd = $conn->prepare("CALL pr_ReativarLocatario(?, ?)");
                $stmtUpd->bind_param("is", $id, $novaSenhaHash);

                if ($stmtUpd->execute()) {
                    $sweetAlert = [
                        'icon' => 'success',
                        'title' => 'Concluído!',
                        'text' => 'Locatário reativado com sucesso!',
                        'redirect' => 'index.php?page=gerenciarLocatarios'
                    ];
                } else {
                    $sweetAlert = [
                        'icon' => 'error',
                        'title' => 'Erro!',
                        'text' => 'Erro ao reativar locatário: ' . $conn->error
                    ];
                }

                $stmtUpd->close();
            }
        } else {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => 'E-mail não encontrado no sistema.'
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
    <title>Redefinir Senha - Tijucas Open</title>
    <link rel="stylesheet" href="proprietario.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Mesmos estilos do formulário de cadastro para consistência */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.1em;
            background-color: #385c30;
            border-color: #385c30;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2e4b26;
            border-color: #2e4b26;
        }

        .btn-dark {
            margin-bottom: 20px;
            align-self: flex-start;
        }

        input.is-invalid {
            border-color: red;
            background-color: #ffe6e6;
        }

        #erroPass {
            color: red;
            font-size: 0.9em;
            margin-bottom: 5px;
            display: none;
        }

        .senha-container {
            position: relative;
        }

        .toggle-senha {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
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

    <div class="form-container">
      <h2>Redefinir Senha</h2>
      <form method="post" onsubmit="return validarFormulario();">
          <label for="emailREC" class="form-label">Email</label>
          <input type="email" id="emailREC" name="emailREC" class="form-control" required placeholder="Digite seu e-mail" value="<?= htmlspecialchars($email) ?>" readonly />


          <label for="passREC" class="form-label">Nova Senha</label>
          <div id="erroPass">A senha deve ter no mínimo 8 caracteres, incluindo 1 letra maiúscula, 1 número e 1 caractere especial.</div>
          <div class="senha-container">
            <input type="password" id="passREC" name="passREC" class="form-control" required placeholder="Digite a nova senha" />
            <button type="button" id="toggleSenhaREC" class="toggle-senha" aria-label="Mostrar senha">👁</button>
          </div>

          <label for="passConfirm" class="form-label">Confirmar Nova Senha</label>
          <div class="senha-container">
            <input type="password" id="passConfirm" name="passConfirm" class="form-control" required placeholder="Confirme a nova senha" />
            <button type="button" id="toggleSenhaConfirm" class="toggle-senha" aria-label="Mostrar senha">👁</button>
          </div>

          <button type="submit" class="btn btn-primary">Reativar Locatário</button>
      </form>
    </div>
  </div>

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Mostrar/ocultar senha
    function toggleSenha(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        btn.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '👁';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        });
    }
    toggleSenha('passREC', 'toggleSenhaREC');
    toggleSenha('passConfirm', 'toggleSenhaConfirm');

    // Validação de senha forte
    function validarSenha(senha) {
        const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        return regex.test(senha);
    }

    function validarFormulario() {
        const senha = document.getElementById('passREC').value;
        const confirma = document.getElementById('passConfirm').value;
        const erroPass = document.getElementById('erroPass');

        if (!validarSenha(senha)) {
            erroPass.style.display = 'block';
            document.getElementById('passREC').classList.add('is-invalid');
            return false;
        } else {
            erroPass.style.display = 'none';
            document.getElementById('passREC').classList.remove('is-invalid');
        }

        if (senha !== confirma) {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'As senhas não coincidem. Por favor, tente novamente.'
            });
            return false;
        }

        return true;
    }

    // Mostrar SweetAlert se veio mensagem do PHP
    <?php if (isset($sweetAlert)): ?>
        Swal.fire({
            icon: '<?= $sweetAlert['icon']; ?>',
            title: '<?= $sweetAlert['title']; ?>',
            text: '<?= $sweetAlert['text']; ?>'
        }).then(() => {
            <?php if (isset($sweetAlert['redirect'])): ?>
                window.location.href = '<?= $sweetAlert['redirect']; ?>';
            <?php endif; ?>
        });
    <?php endif; ?>
  </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../conteudo_livre/assets/js/alerts_confirmacao.js"></script>
</body>
</html>
