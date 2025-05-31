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
        $sweetAlert = [
            'icon' => 'success',
            'title' => 'Sucesso!',
            'text' => 'Loja cadastrada com sucesso!',
            'redirect' => 'index.php?page=gerenciarLojas'
        ];
    } else {
        $error = addslashes(htmlspecialchars($stmt->error));
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Erro!',
            'text' => "Erro ao cadastrar loja: {$error}"
        ];
    }
    $stmt->close();
}

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
    WHERE lo.espaco_id IS NULL AND c.contrato_status = 'Ativo'"; 
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
    <title>Cadastro de Loja</title>
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
            position: relative; /* Para posicionar o botão voltar de forma absoluta */
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

        /* Estilo para o input file */
        input[type="file"] {
            padding-top: 8px; /* Ajuste para melhor alinhamento visual */
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

        /* Estilo para o botão Voltar dentro do form-container */
        .btn-back-form {
            position: absolute;
            top: 20px; /* Ajuste a distância do topo */
            left: 20px; /* Ajuste a distância da esquerda */
            background-color: black; /* Uma cor mais neutra para "voltar" */
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            display: flex; /* Para alinhar o ícone e o texto */
            align-items: center;
            gap: 5px; /* Espaço entre o ícone e o texto */
        }

        .btn-back-form:hover {
            background-color: #5a6268;
            color: white; /* Manter a cor do texto branca no hover */
        }

        /* Ajuste para o botão submit 'accept' do seu formulário (se você o tiver com essa classe) */
        .btn.accept {
            background-color: #2e4d41; /* Cor verde para o botão de aceitar/cadastrar */
            border-color: #2e4d41;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px; /* Espaço acima do botão */
            transition: background-color 0.3s ease;
        }

        .btn.accept:hover {
            background-color: #4e7d69;
            border-color: #4e7d69;
        }
    </style>
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
            <a href="index.php?page=gerenciarLocatarios"
                class="<?= (isset($_GET['page']) && $_GET['page'] == 'gerenciarLocatarios') ? 'ativo' : ''; ?>">Gerenciar
                Locatários</a>
            <a href="index.php?page=gerenciarContratos"
                class="<?= (isset($_GET['page']) && $_GET['page'] == 'gerenciarContratos') ? 'ativo' : ''; ?>">Gerenciar
                Contratos</a>
            <a href="index.php?page=gerenciarLojas"
                class="<?= (isset($_GET['page']) && $_GET['page'] == 'gerenciarLojas') ? 'ativo' : ''; ?>">Gerenciar
                Lojas</a>
            <a href="index.php?page=gerenciarEspacos"
                class="<?= (isset($_GET['page']) && $_GET['page'] == 'gerenciarEspacos') ? 'ativo' : ''; ?>">Gerenciar
                Espaços</a>
        </nav>


        <div class="logout">
            <a href="../logout.php"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <div class="form-container">
            <a href="index.php?page=gerenciarLojas" class="btn-back-form">
                <span class="bi-arrow-left"></span> Voltar
            </a>
            <h2>Cadastro de Nova Loja</h2>
            <form action="cadLojas.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Empresa (Locatário):</label>
                    <select id="empresa_select" name="loja_nome" class="form-select" onchange="preencherEspacoEPiso()"
                        required>
                        <option value="">Selecione a empresa</option>
                        <?php foreach ($contratos as $contrato): ?>
                            <option value="<?= htmlspecialchars($contrato['empresa_nome']) ?>"
                                data-espaco="<?= $contrato['espaco_id'] ?>"
                                data-piso="<?= htmlspecialchars($contrato['espaco_piso']) ?>">
                                <?= htmlspecialchars($contrato['empresa_nome']) ?> - Espaço
                                <?= htmlspecialchars($contrato['espaco_id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Telefone da Loja:</label>
                    <input type="text" name="loja_telefone" class="form-control" required
                        placeholder="Digite o Telefone" maxlength="15" oninput="mascararTelefone(this)">
                </div>

                <div class="mb-3">
                    <label class="form-label">Andar:</label>
                    <input type="text" name="loja_andar" class="form-control" readonly required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Espaço:</label>
                    <input type="text" name="espaco_id" class="form-control" readonly required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo da Loja:</label>
                    <select name="loja_tipo" class="form-select" required>
                        <option value="Alimentação">Restaurante</option>
                        <option value="Roupas">Roupas</option>
                        <option value="Esportes">Esportes</option>
                        <option value="Livros">Livros</option>
                        <option value="Jóias">Jóias</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Logo da Loja:</label>
                    <input type="file" name="loja_logo" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar Loja</button>
            </form>
        </div>
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

    <?php if (isset($sweetAlert)): ?>
        <script>
            const sweetAlertData = <?= json_encode($sweetAlert) ?>;
        </script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../conteudo_livre/assets/js/alerts.js"></script>
</body>

</html>