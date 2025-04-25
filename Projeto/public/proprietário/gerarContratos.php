<?php
/* ANÁLISE NECESSÁRIA */

























require_once __DIR__ . '/../../vendor/autoload.php';
use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean(); // limpa qualquer saída anterior
    ob_start(); // inicia buffer de saída

    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $loja = $_POST['loja'];
    $espaço = $_POST['espaço'];
    $modelo = $_POST['modelo']; 
    $data = DateTime::createFromFormat('Y-m-d', $_POST['data']);
    $dataBr = $data->format('d/m/Y');

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
</head>
<body class="p-4">

  <a href="index.php?page=gerenciarContratos" class="btn btn-dark mb-3">Voltar</a>
  <h2>Preencha os dados do locatário para criar o contrato:</h2>
    
  <form method="POST" action="addContrato.php">

      <div class="mb-3">
          <label class="form-label">CNPJ:</label>
          <input type="text" name="cnpj" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Nome:</label>
          <input type="text" name="nome" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Nome da Loja:</label>
          <input type="text" name="loja" class="form-control" required>
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
          <label class="form-label">Data de Início: (Contrato valido por 12 meses)</label>
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

