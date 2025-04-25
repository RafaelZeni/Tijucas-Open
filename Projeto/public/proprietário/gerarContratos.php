<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require '../../app/database/connection.php';
use setasign\Fpdi\Fpdi;

$obj = conecta_db();
$querySelect = "SELECT empresa_cnpj, empresa_nome FROM tb_locatarios";
$resultadoCnpjs = $obj->query($querySelect);

$empresas = [];
while ($linha = $resultadoCnpjs->fetch_object()) {
    $empresas[] = [
        'cnpj' => $linha->empresa_cnpj,
        'loja' => $linha->empresa_nome
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : '';
    $loja = isset($_POST['loja']) ? $_POST['loja'] : '';
    $espaço = isset($_POST['espaço']) ? $_POST['espaço'] : '';
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $data = isset($_POST['data']) ? DateTime::createFromFormat('Y-m-d', $_POST['data']) : null;
    $dataFormatadaBanco = $data ? $data->format('Y-m-d H:i:s') : ''; // Formato para DATETIME

    if (empty($nome) || empty($cnpj) || empty($modelo) || !$data) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios!');</script>";
        exit;
    }

    // Buscar o ID da empresa pelo CNPJ
    $stmt = $obj->prepare("SELECT empresa_id FROM tb_locatarios WHERE empresa_cnpj = ?");
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
    $insert = $obj->prepare("INSERT INTO tb_contrato (empresa_id, data_inicio, nome_loc) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $empresa_id, $dataFormatadaBanco, $nome);
    if ($insert->execute()) {
        
        // Inserção bem-sucedida (opcional: exibir mensagem)
        // echo "<script>alert('Dados do contrato salvos com sucesso!');</script>";
    } else {
        // Erro na inserção
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
            $pdf->Write(10, utf8_decode($espaço));

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
    <meta charset="UTF-8">
    <title>Gerar Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body class="p-4">

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
            <label class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome da Loja:</label>
            <input type="text" name="loja" id="loja" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Selecione o espaço alugado:</label>
            <select name="espaço" class="form-select" required>
                <?php
                    for ($i = 1; $i <= 24; $i++) {
                        echo "<option value='Espaço $i'>Espaço $i</option>";
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

</body>
</html>