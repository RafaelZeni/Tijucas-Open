<?php
// Carrega automaticamente todas as bibliotecas instaladas pelo Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Usa a classe Fpdi para importar e manipular arquivos PDF existentes
use setasign\Fpdi\Fpdi;

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $endereco = $_POST['endereco'];
    $modelo = $_POST['modelo']; // ex: "assets/imgs/1.pdf"
    $dataBr = DateTime::createFromFormat('Y-m-d', $_POST['data'])->format('d/m/Y');
 // nova data

    // Gera o caminho completo até o arquivo PDF modelo
    $caminhoPdf = __DIR__ . '/' . $modelo;

    // Cria uma nova instância do FPDI
    $pdf = new Fpdi();

    // Conta quantas páginas o PDF possui
    $totalPaginas = $pdf->setSourceFile($caminhoPdf);

    // Loop por todas as páginas do PDF
    for ($i = 1; $i <= $totalPaginas; $i++) {
        $pdf->AddPage();                 // Cria uma nova página no PDF final
        $tpl = $pdf->importPage($i);     // Importa a página $i do contrato
        $pdf->useTemplate($tpl);         // Usa a página importada como fundo

        $pdf->SetFont('Arial');
        $pdf->SetTextColor(0, 0, 0);

        // Preenche os dados na primeira página
        if ($i === 1) {
            $pdf->SetXY(30, 104);
            $pdf->Write(10, utf8_decode($nome));

            $pdf->SetXY(30, 147);
            $pdf->Write(10, utf8_decode($cnpj));

            $pdf->SetXY(30, 167);
            $pdf->Write(10, utf8_decode($endereco));
        }

        // Preenche a data no final da segunda página
        if ($i === 2) {
            $pdf->SetXY(44, 241); // Ajuste se quiser mudar a posição
            $pdf->Write(10, utf8_decode( $dataBr));
        }
    }

    // Gera e envia o PDF para download com o nome personalizado
    $pdf->Output('D', "Contrato_" . utf8_decode($nome) . ".pdf");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerar Contrato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

  <a href="index.php?page=gerenciarContratos" class="btn btn-dark mb-3">Voltar</a>

  <h2>Preencha os dados do locatário e selecione o contrato:</h2>
    
  <form method="POST">
      <div class="mb-3">
          <label class="form-label">Nome:</label>
          <input type="text" name="nome" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">CNPJ:</label>
          <input type="text" name="cnpj" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Endereço:</label>
          <input type="text" name="endereco" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Data:</label>
          <input type="date" name="data" class="form-control" required>
      </div>

      <div class="mb-3">
          <label class="form-label">Selecione o tipo de contrato:</label>
          <select name="modelo" class="form-select" required>
              <option value="assets/imgs/1.pdf">MIDIA PREMIUM</option>
              <option value="assets/imgs/2.pdf">MIDIA INTERMEDIÁRIO</option>
          </select>
      </div>

      <button type="submit" class="btn btn-primary">Gerar e Baixar Contrato</button>
  </form>

</body>
</html>

