<?php
require '../../app/database/connection.php';

$conn = conecta_db();

$boleto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sweetAlert = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprovante"])) {
    $arquivo = $_FILES["comprovante"];
    $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $permitidos)) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Formato não permitido!',
            'text' => 'Por favor, envie um arquivo PDF ou imagem (jpg, jpeg, png).'
        ];
    } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Arquivo muito grande!',
            'text' => 'O arquivo deve ter no máximo 5MB.'
        ];
    } else {
        $nome_arquivo = uniqid("comprovante_") . "." . $extensao;
        $destino = "comprovantes/" . $nome_arquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            $stmt = $conn->prepare("CALL pr_EnviarComprovante(?, ?)");
            $stmt->bind_param("is", $boleto_id, $nome_arquivo);
            if ($stmt->execute()) {
                // Envio do comprovante foi bem-sucedido
                $sweetAlert = [
                    'icon' => 'success',
                    'title' => 'Sucesso!',
                    'text' => 'Comprovante enviado com sucesso!',
                    'redirect' => 'index.php'
                ];

                // --- VERIFICAR se todos os boletos do contrato estão "Enviado"
                
                // 1. Pegar o contrato_id a partir do boleto_id
                $stmtContrato = $conn->prepare("SELECT contrato_id FROM tb_boletos WHERE boleto_id = ?");
                $stmtContrato->bind_param("i", $boleto_id);
                $stmtContrato->execute();
                $stmtContrato->bind_result($contrato_id);
                $stmtContrato->fetch();
                $stmtContrato->close();

                // 2. Verificar se existe algum boleto PENDENTE nesse contrato
                $stmtPendentes = $conn->prepare("SELECT COUNT(*) FROM tb_boletos WHERE contrato_id = ? AND status_boleto != 'Enviado'");
                $stmtPendentes->bind_param("i", $contrato_id);
                $stmtPendentes->execute();
                $stmtPendentes->bind_result($pendentes);
                $stmtPendentes->fetch();
                $stmtPendentes->close();

                // 3. Se não há pendentes, chama a procedure para remover o contrato
                if ($pendentes == 0) {
                    $conn->query("CALL pr_RemoverContrato($contrato_id)");
                }
            } else {
                $sweetAlert = [
                    'icon' => 'error',
                    'title' => 'Erro no banco de dados!',
                    'text' => 'Não foi possível salvar o comprovante.'
                ];
            }
            $stmt->close();
        } else {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Erro ao enviar!',
                'text' => 'Falha ao mover o arquivo para o destino.'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Enviar Comprovante</title>
    <link rel="stylesheet" href="locatario.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

    <button class="hamburger-menu" aria-label="Abrir menu" aria-expanded="false">
      &#9776; </button>

    <div class="sidebar">
        <div class="logo">
            <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
        </div>


        <nav>
            <a href="index.php" class="ativo">Início</a>
            <a href="index.php?page=visualizarEspacos">Visualizar Espaços</a>
            <a href="index.php?page=gestaoContratos">Gestão de Contrato</a>
        </nav>

        <div class="logout">
            <a href="../logout.php"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
        <h2>Enviar Comprovante de Pagamento</h2>


        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="comprovante" class="form-label">Selecionar Comprovante (PDF ou imagem até 5MB):</label>
                <input type="file" name="comprovante" id="comprovante" class="form-control" required>
            </div>
            <input type="hidden" name="boleto_id" value="<?php echo $boleto_id; ?>">
            <button type="submit" class="btn btn-success">Enviar</button>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($sweetAlert): ?>
      <script>
        Swal.fire({
          icon: '<?= $sweetAlert["icon"] ?>',
          title: '<?= $sweetAlert["title"] ?>',
          text: '<?= $sweetAlert["text"] ?>',
        }).then(() => {
          <?php if (!empty($sweetAlert['redirect'])): ?>
              window.location.href = "<?= $sweetAlert['redirect'] ?>";
          <?php endif; ?>
        });
      </script>
    <?php endif; ?>


    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>