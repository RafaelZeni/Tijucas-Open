<?php
require '../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['emailREC'];
    $novaSenha = $_POST['passREC'];
    $confirmaSenha = $_POST['passConfirm'];

    if ($novaSenha !== $confirmaSenha) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Erro!',
            'text' => 'As senhas não coincidem. Por favor, tente novamente.'
        ];
    } else {
        $conn = conecta_db();

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

                $stmt = $conn->prepare("UPDATE tb_logins SET senha_usu = ? WHERE email_usu = ?");
                $stmt->bind_param("ss", $novaSenhaHash, $email);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $sweetAlert = [
                            'icon' => 'success',
                            'title' => 'Concluído!',
                            'text' => 'Nova senha definida com sucesso!',
                            'redirect' => 'index.php?page=entrar'
                        ];
                    } else {
                        $sweetAlert = [
                            'icon' => 'error',
                            'title' => 'Erro!',
                            'text' => 'E-mail de usuário não encontrado no sistema.'
                        ];
                    }
                } else {
                    $sweetAlert = [
                        'icon' => 'error',
                        'title' => 'Erro!',
                        'text' => 'Erro ao atualizar senha: ' . $conn->error
                    ];
                }
            }
        } else {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => 'E-mail de usuário não encontrado no sistema.'
            ];
        }

        $stmt->close();
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Recuperar Senha - Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="login">

<section class="banner">
    <img src="conteudo_livre/assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Recuperar Senha</h3>
            <form method="post" class="forms" onsubmit="return validarFormulario();">
                <label for="emailREC">Email</label>
                <input type="email" id="emailREC" name="emailREC" required placeholder="Digite seu e-mail">

                <label for="passREC">Nova Senha</label>
                <div id="erroPass" style="color: red; display: none; font-size: 0.9em; margin-bottom: 5px;">
                    A senha deve ter no mínimo 8 caracteres, incluindo 1 letra maiúscula, 1 número e 1 caractere especial.
                </div>
                <input type="password" id="passREC" name="passREC" required placeholder="Digite a nova senha">

                <label for="passConfirm">Confirmar Nova Senha</label>
                <input type="password" id="passConfirm" name="passConfirm" required placeholder="Confirme sua nova senha" oninput="validarSenha(this)">

                <button type="submit" class="enviar">Atualizar Senha</button>

                <a class="recsenha" href="index.php?page=entrar">Voltar para Login</a>
            </form>
        </section>
    </div> 
</section>
<?php if (isset($sweetAlert)): ?>
  <script>
    const sweetAlertData = <?= json_encode($sweetAlert) ?>;
  </script>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="conteudo_livre/assets/js/alerts.js"></script>

<script>
    function validarFormulario() {
      //Senha
      const passInput = document.getElementById("passREC");
      const erroPass = document.getElementById("erroPass");

      if (!validarSenha(passInput.value)) {
        erroPass.style.display = "block";
        passInput.classList.add("is-invalid");
        passInput.focus();
        return false;
      }
      erroPass.style.display = "none";
      passInput.classList.remove("is-invalid");
      return true;
    }


    function validarSenha(senha) {
        const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        return regex.test(senha);
    }
</script>
</body>
</html>
