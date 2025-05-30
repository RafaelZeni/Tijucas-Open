<!--Página de Login, onde você insere seus dados para que seja dirigido 
para a respectiva página. Verifica se já o usuário já possui cadastro, 
caso não possua, informa que usuário ou senha estão errados. Tem a opção
de se dirigir para a página de recuperar senha-->

<?php
session_start();
require '../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['emailLog'];
    $senha = $_POST['passLog'];

    $conn = conecta_db(); // usa sua função personalizada

    $stmt = $conn->prepare("SELECT logins_id, senha_usu, tipo_usu, auth_secret FROM tb_logins WHERE email_usu = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) { // verifica se o usuário existe, e pega seus dados
        $row = $resultado->fetch_assoc();

        if (password_verify($senha, $row['senha_usu'])) { // verifica se a senha está correta
            $_SESSION['logins_id'] = $row['logins_id'];
            $_SESSION['tipo_usu'] = $row['tipo_usu'];

            if (!empty($row['auth_secret'])) {
                header("Location: ../conteudo_livre/autenticacao/verificar_2fa.php");
                exit;

            } else {
                $_SESSION['2fa_passed'] = true;

                switch ($row['tipo_usu']) { // redireciona para a página correta
                    case 'proprietario':
                        header("Location: ./proprietário/index.php");
                        break;
                    case 'locatario':
                        header("Location: ./locatário/index.php");
                        break;
                    case 'inativo':
                        break;

                    default:
                        header("Location: index.php?page=entrar");
                        break;
                }
                exit;
            }
        } else { // se a senha estiver errada, exibe mensagem de erro
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => 'Usuário ou senha incorretos!'
            ];
        }
    } else {
        $sweetAlert = [
            'icon' => 'error', // se o usuário não existir, exibe mensagem de erro
            'title' => 'Erro!',
            'text' => "E-mail de usuário não encontrado no sistema."
        ];
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>

<body class="login">
    <header class="header">
        <div class="trio">

        </div>
        <div class="trio">
            <nav class="botoesMenu">
                <?php
                $page = $_GET['page'] ?? 'inicio'; ?>
                <ul>
                    <li class="<?= ($page == 'inicio') ? 'ativo' : '' ?>">
                        <a href="/Tijucas-Open/Projeto/public/index.php">
                            <h2>Início</h2>
                        </a>
                    </li>
                    <li class="<?= ($page == 'lojas') ? 'ativo' : '' ?>">
                        <a href="index.php?page=lojas">
                            <h2>Lojas</h2>
                        </a>
                    </li>
                    <li class="<?= ($page == 'mapa') ? 'ativo' : '' ?>">
                        <a href="index.php?page=mapa">
                            <h2>Mapa-interativo</h2>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="trio">

        </div>
    </header>
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerCachoeira.jpg" alt="Estacionamento Tijucas Open">
        <div class="quadrado">
            <section class="form-container">
                <h3>Entrar</h3>
                <form method="post" class="forms">
                    <label for="emailLog">Email</label>
                    <input type="email" id="emailLog" name="emailLog" required placeholder="Digite seu e-mail">

                    <label for="passLog">Senha</label>
                    <div class="senha-container">
                        <input type="password" id="passLog" name="passLog" required placeholder="Digite sua senha">
                        <button type="button" id="toggleSenha" class="toggle-senha"
                            aria-label="Mostrar senha">👁</button>
                    </div>

                    <button type="submit" class="enviar">Entrar</button>

                    <a class="recsenha" href="index.php?page=recsenha">Esqueceu sua senha?</a>
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
        // Função para mostrar a senha ao clicar no ícone
        document.getElementById('toggleSenha').addEventListener('click', function () {
            const senhaInput = document.getElementById('passLog');
            const tipoAtual = senhaInput.getAttribute('type');

            if (tipoAtual === 'password') {
                senhaInput.setAttribute('type', 'text');
                this.textContent = '👁';
            } else {
                senhaInput.setAttribute('type', 'password');
                this.textContent = '👁';
            }
        });
    </script>
</body>

</html>