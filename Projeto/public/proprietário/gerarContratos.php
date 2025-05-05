<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require '../../app/database/connection.php';
use setasign\Fpdi\Fpdi;

$conn = conecta_db();

// Buscar empresas (CNPJs) que não têm contrato ainda
$querySelect = "
    SELECT l.empresa_cnpj, l.empresa_nome 
    FROM tb_locatarios l
    LEFT JOIN tb_contrato c ON l.empresa_id = c.empresa_id
    WHERE c.empresa_id IS NULL";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        echo "<script>alert('CNPJ não encontrado na tabela de locatários!'); window.location.href = 'index.php?page=gerarContratos';</script>";
        exit;
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
    } else {
        echo "<script>alert('Erro ao salvar dados do contrato: " . $insert->error . "');</script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="proprietario.css">
    <script>
        const empresas = <?php echo json_encode($empresas); ?>;
        
        function atualizarLoja() {
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
    <a href="index.php?page=gerenciarContratos" class="btn btn-dark mb-3">Voltar</a>
    <h2>Preencha os dados do locatário para criar o contrato:</h2>

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
  </body>
</html>