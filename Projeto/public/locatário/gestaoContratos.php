<?php
// session_start(); // Se index.php já iniciou, não precisa aqui.
require '../../app/database/connection.php'; // Garanta que o caminho está correto

$conn = conecta_db();
$contratos = []; // Inicializa como array vazio

if ($conn && isset($_SESSION['logins_id'])) { // Verifica conexão e se logins_id existe
    $logins_id = $_SESSION['logins_id'];

    $sqlEmpresa = "SELECT empresa_id FROM tb_locatarios WHERE logins_id = ?";
    $stmtEmpresa = $conn->prepare($sqlEmpresa);
    
    if ($stmtEmpresa) {
        $stmtEmpresa->bind_param("i", $logins_id);
        $stmtEmpresa->execute();
        $resultEmpresa = $stmtEmpresa->get_result();

        if ($rowEmpresa = $resultEmpresa->fetch_assoc()) {
            $empresa_id = $rowEmpresa['empresa_id'];

            $sql = "SELECT contrato_id, espaco_id, data_inicio, nome_loc, valor_mensal, contrato_status
                    FROM tb_contrato
                    WHERE empresa_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $empresa_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($contrato_item = $result->fetch_assoc()) {
                    $contratos[] = $contrato_item; // Adiciona ao array
                }
                $stmt->close();
            } else {
                // erro ao preparar $stmt
            }
        } else {
            // Não encontrou empresa_id, pode adicionar um alerta ou mensagem
            // echo "<script>alert('Erro ao localizar empresa.'); window.location.href='index.php';</script>";
            // exit;
        }
        $stmtEmpresa->close();
    } else {
        // erro ao preparar $stmtEmpresa
    }
    $conn->close();
} else {
    // Erro de conexão ou logins_id não definido
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Contrato</title>
    <link rel="stylesheet" href="locatario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <button class="hamburger-menu" aria-label="Abrir menu" aria-expanded="false">
      &#9776;
    </button>

    <div class="sidebar">
        <div class="logo">
            <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
        </div>

        <?php $activePage = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
        <nav>
            <a href="index.php" class="<?= ($activePage == 'home') ? 'ativo' : ''; ?>">Início</a>
            <a href="index.php?page=visualizarEspacos" class="<?= ($activePage == 'visualizarEspacos') ? 'ativo' : ''; ?>">Visualizar Espaços</a>
            <a href="index.php?page=gestaoContratos" class="<?= ($activePage == 'gestaoContratos') ? 'ativo' : ''; ?>">Gestão de Contrato</a>
        </nav>

        <div class="logout">
            <a href="../logout.php"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h1>Meu Contrato</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                    <div class="table-wrapper">
                        <table class="table table-striped-green text-center">
                            <thead>
                                <tr>
                                    <th>ID do Contrato</th>
                                    <th>ID do Espaço</th>
                                    <th>Data de Início</th>
                                    <th>Data de Fim</th>
                                    <th>Responsável</th>
                                    <th>Valor do Contrato</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($contratos)): ?>
                                    <?php foreach ($contratos as $contrato):
                                        $data_inicio_obj = new DateTime($contrato['data_inicio']);
                                        $data_inicio_formatada = $data_inicio_obj->format('d-m-Y');

                                        $data_fim_obj = clone $data_inicio_obj;
                                        $data_fim_formatada = $data_fim_obj->add(new DateInterval('P12M'))->format('d-m-Y'); // Corrigido para data_fim_formatada
                                        $valor_formatado = number_format($contrato['valor_mensal'], 2, ',', '.');
                                    ?>
                                        <tr>
                                            <td data-label="ID Contrato"><?= htmlspecialchars($contrato['contrato_id']) ?></td>
                                            <td data-label="ID Espaço"><?= htmlspecialchars($contrato['espaco_id']) ?></td>
                                            <td data-label="Data Início"><?= htmlspecialchars($data_inicio_formatada) ?></td>
                                            <td data-label="Data Fim"><?= htmlspecialchars($data_fim_formatada) ?></td>
                                            <td data-label="Responsável"><?= htmlspecialchars($contrato['nome_loc']) ?></td>
                                            <td data-label="Valor Contrato">R$<?= htmlspecialchars($valor_formatado) ?></td>
                                            <td> <?=$contrato['contrato_status']?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">Nenhum contrato encontrado ou erro ao buscar dados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
      const hamburgerButton = document.querySelector('.hamburger-menu');
      const sidebar = document.querySelector('.sidebar');

      if (hamburgerButton && sidebar) {
        hamburgerButton.addEventListener('click', () => {
          sidebar.classList.toggle('open');
          const isExpanded = sidebar.classList.contains('open');
          hamburgerButton.setAttribute('aria-expanded', isExpanded);
          if (isExpanded) {
            hamburgerButton.setAttribute('aria-label', 'Fechar menu');
          } else {
            hamburgerButton.setAttribute('aria-label', 'Abrir menu');
          }
        });
      }
    </script>
</body>
</html>