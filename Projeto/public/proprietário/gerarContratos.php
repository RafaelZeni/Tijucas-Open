<!-- Página de gestão de contratos, permite criar um contrato se 
já houver um locatário cadastrado através de seu CNPJ. É necessário 
inserir o nome do responsável, selecionar um dos CNPJ já cadastrados, 
selecionar o espaço de locação, colocar a data de início.-->

<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require '../../app/database/connection.php';
use setasign\Fpdi\Fpdi;
use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Agente;

$conn = conecta_db();

// Buscar empresas (CNPJs) que não têm contrato ainda
$querySelect = "
    SELECT l.empresa_id, l.empresa_nome, l.empresa_cnpj
    FROM tb_locatarios l
    LEFT JOIN tb_contrato c 
        ON l.empresa_id = c.empresa_id AND c.contrato_status = 'Ativo'
    JOIN tb_logins lg 
        ON l.logins_id = lg.logins_id
    WHERE c.contrato_id IS NULL
    AND lg.tipo_usu = 'locatario'";

$resultadoCnpjs = $conn->query($querySelect);

$empresas = [];
while ($linha = $resultadoCnpjs->fetch_object()) {
    $empresas[] = [
        'cnpj' => $linha->empresa_cnpj,
        'loja' => $linha->empresa_nome
    ];
}

// Buscar espaços disponíveis
$queryEspacos = "SELECT espaco_id, espaco_area FROM tb_espacos WHERE espaco_status = 'Disponível'";
$resultadoEspacos = $conn->query($queryEspacos);

$espacos = [];
while ($linha = $resultadoEspacos->fetch_object()) {
    $espacos[] = [
        'id' => $linha->espaco_id,
        'area' => $linha->espaco_area
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //função para verificar se o formulário foi enviado
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : '';
    $loja = isset($_POST['loja']) ? $_POST['loja'] : '';
    $espaco = isset($_POST['espaco']) ? $_POST['espaco'] : '';
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $data = isset($_POST['data']) ? DateTime::createFromFormat('Y-m-d', $_POST['data']) : null;
    $dataFormatadaBanco = $data ? $data->format('Y-m-d H:i:s') : '';

    if (empty($nome) || empty($cnpj) || empty($modelo) || !$data || empty($espaco)) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios!');</script>";
        exit;
    }

    // Buscar o ID da empresa pelo CNPJ
    $stmt = $conn->prepare("SELECT empresa_id FROM tb_locatarios WHERE empresa_cnpj = ?");
    $stmt->bind_param("s", $cnpj);
    $stmt->execute();
    $stmt->bind_result($empresa_id);
    $stmt->fetch();
    $stmt->close();

    if (!$empresa_id) {
        $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "CNPJ não encontrado na tabela de locatários!",
                'redirect' => 'index.php?page=gerarContratos'];
    }

    // Inserir na tabela tb_contrato
    $insert = $conn->prepare("CALL pr_CriarContrato(?, ?, ?, ?)");
    $insert->bind_param("issi", $empresa_id, $dataFormatadaBanco, $nome, $espaco);

    if ($insert->execute()) {
        // Atualizar espaço para 'Alugado'
        $updateEspaco = $conn->prepare("UPDATE tb_espacos SET espaco_status = 'Alugado' WHERE espaco_id = ?");
        $updateEspaco->bind_param("i", $espaco);
        $updateEspaco->execute();
        $updateEspaco->close();

        $puxarContrato_id = $conn->query("SELECT LAST_INSERT_ID() AS contrato_id");
        $linhaContrato_id = $puxarContrato_id->fetch_object();
        $contrato_id = $linhaContrato_id->contrato_id;

        if(!$contrato_id){
            die('Erro: contrato_id não obtido após criação de contrato!');
        }

        $pagador = new Agente($nome, $cnpj);
        $cedente = new Agente('Tijucas Open', '86.398.751/0001-23', 'Rua Fictícia 123', 'Tijucas do Sul', 'PR', '00000-000');

        $valorMensal = 3000;

        for ($i = 0; $i < 12; $i++){
            $vencimento = (clone $data)->modify("+$i months");
            $vencimentoFormatado = $vencimento->format('Y-m-d');

            $numeroDocumento = $empresa_id . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $boleto = new BancoDoBrasil([
                'dataVencimento' => $vencimento,
                'valor' => $valorMensal,
                'numero' => $i + 1,
                'sacado' => $pagador,
                'cedente' => $cedente,
                'agencia' => 1234,
                'carteira' => 18,
                'conta' => 123456,
                'convenio' => 123456,
                'numeroDocumento' => (string)$numeroDocumento
            ]);

            $linhaDigitavel = $boleto->getLinhaDigitavel();

            $stmt = $conn->prepare("INSERT INTO tb_boletos (contrato_id, numero_documento, valor, vencimento, banco, linha_digitavel) VALUES (?, ?, ?, ?, ?, ?)");
            $banco = "BancoDoBrasil";
            $stmt->bind_param("isdsss", $contrato_id, $numeroDocumento, $valorMensal, $vencimentoFormatado, $banco, $linhaDigitavel);

            $stmt->execute();
            $stmt->close();
        }
    } else {
        $error = addslashes(htmlspecialchars($insert->error));
        $sweetAlert = ['icon' => 'error',
                'title' => 'Erro!',
                'text' => "Erro ao salvar dados do contrato: {$error}"];
    }
    $insert->close();

    ob_clean();
    ob_start();

    $caminhoPdf = __DIR__ . '/' . $modelo;
    $pdf = new Fpdi();
    $totalPaginas = $pdf->setSourceFile($caminhoPdf);

    for ($i = 1; $i <= $totalPaginas; $i++) {
        $pdf->AddPage();
        $tpl = $pdf->importPage($i);
        $pdf->useTemplate($tpl);

        $pdf->SetFont('Arial');
        $pdf->SetTextColor(0, 0, 0);

        if ($i === 1) {
            $pdf->SetXY(112, 117);
            $pdf->Write(10, utf8_decode($nome));

            $pdf->SetXY(30, 117);
            $pdf->Write(10, utf8_decode("Cristiano Ramos de Lima"));

            $pdf->SetXY(128, 69);
            $pdf->Write(10, utf8_decode($cnpj));

            $pdf->SetXY(61, 229);
            $pdf->Write(10, utf8_decode("Espaço $espaco"));

            $pdf->SetXY(50, 235);
            $pdf->Write(10, utf8_decode($loja));
        }

        if ($i === 5) {
            $dataBr = $data->format('d/m/Y');
            $pdf->SetXY(45, 163);
            $pdf->Write(10, utf8_decode(" $dataBr"));
        }
    }

    $pdf->Output('D', "Contrato_" . utf8_decode($nome) . ".pdf");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Gerar Contrato</title>
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
            margin-top: -20px; /* Espaço acima do formulário */
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-control,
        .form-select {
            border-radius: 5px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            font-size: 1.1em;
            background-color: #385c30; /* Cor do botão primário */
            border-color: #385c30;
        }

        .btn-primary:hover {
            background-color: #2e4b26;
            border-color: #2e4b26;
        }

        .btn-dark {
            margin-bottom: 20px; /* Espaço abaixo do botão "Voltar" */
            align-self: flex-start; /* Alinha o botão "Voltar" à esquerda do content */
        }
    </style>
    <script>
        const empresas = <?php echo json_encode($empresas); ?>;
        
        function atualizarLoja() { // Função para atualizar o campo "loja" com base no CNPJ selecionado
            const cnpjSelecionado = document.getElementById('cnpj').value;
            const campoLoja = document.getElementById('loja');

            const empresa = empresas.find(e => e.cnpj === cnpjSelecionado);
            if (empresa) {
                campoLoja.value = empresa.loja;
            } else {
                campoLoja.value = '';
            }
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
    <a href="index.php?page=gerenciarContratos" class="btn btn-dark mb-3">Voltar</a>
    
        <div class="form-container"> <h2>Criação de Contrato:</h2>

    <form method="POST" action="">

        <div class="mb-3">
            <label class="form-label">CNPJ:</label>
            <select name="cnpj" id="cnpj" class="form-select" onchange="atualizarLoja()" required>
                <option value="">Selecione um CNPJ</option>
                <?php
                foreach ($empresas as $e) {
                    echo "<option value='{$e['cnpj']}'>{$e['cnpj']} - {$e['loja']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome do Responsável:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome da Loja:</label>
            <input type="text" name="loja" id="loja" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Selecione o espaço alugado:</label>
            <select name="espaco" class="form-select" required>
                <option value="">Selecione um espaço</option>
                <?php
                foreach ($espacos as $e) {
                    echo "<option value='{$e['id']}'>Espaço {$e['id']} - {$e['area']} m²</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Início: (Contrato válido por 12 meses)</label>
            <input type="date" name="data" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de contrato:</label>
            <select name="modelo" class="form-select" required>
                <option value="assets/imgs/1.pdf">CONTRATO DE LOCAÇÃO</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Gerar e Baixar Contrato</button>
    </form>
    </div>
    </div>
  </body>
</html>