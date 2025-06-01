<?php
require '../../app/database/connection.php'; 

$empresa_nome = "Visitante"; 
$auth_secret = null;

if (isset($_SESSION['logins_id'])) {
    $conn = conecta_db();
    if ($conn) {
        $logins_id = $_SESSION['logins_id'];

        // Buscar dados da empresa
        $query = "SELECT empresa_id, empresa_nome FROM tb_locatarios WHERE logins_id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $logins_id);
            $stmt->execute();
            $stmt->bind_result($empresa_id_db, $empresa_nome_db);
            if ($stmt->fetch()) {
                $empresa_id = $empresa_id_db;
                $empresa_nome = $empresa_nome_db; 
            }
            $stmt->close();
        } else {
            error_log("Erro ao preparar a query: " . $conn->error);
        }

        // Buscar auth_secret para 2FA
        $query2fa = "SELECT auth_secret FROM tb_logins WHERE logins_id = ?";
        $stmt2fa = $conn->prepare($query2fa);
        if ($stmt2fa) {
            $stmt2fa->bind_param("i", $logins_id);
            $stmt2fa->execute();
            $stmt2fa->bind_result($auth_secret_db);
            if ($stmt2fa->fetch()) {
                $auth_secret = $auth_secret_db;
            }
            $stmt2fa->close();
        }
    } else {
        error_log("Erro ao conectar ao banco de dados.");
    }
} else {
    error_log("logins_id não encontrado na sessão.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Perfil do Locatário</title>
    <link rel="stylesheet" href="locatario.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

    <button class="hamburger-menu" aria-label="Abrir menu" aria-expanded="false">&#9776;</button>

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
        <h1>Bem-Vindo <?php echo htmlspecialchars($empresa_nome); ?></h1>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h1>Gerenciar Boletos</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <!-- Botão Ativar 2FA ou texto "2FA ativa" -->
                    <?php if (empty($auth_secret)) : ?>
                        <a href="index.php?page=ativar2fa" class="btn btn-secondary mb-3">Ativar 2FA</a>
                    <?php else : ?>
                        <span class="btn btn-success mb-3" style="cursor: default;">2FA ativa</span>
                    <?php endif; ?>
                    <a href="index.php?page=desativar2fa" class="btn btn-danger mb-3">Desativar 2FA</a>

                    <h2>Boletos Pendentes</h2>
                    <div class="table-wrapper">
                        <table class="table table-striped-green text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contrato</th>
                                    <th>Número do Boleto</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Banco</th>
                                    <th>Gerar</th>
                                    <th>Comprovante</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT b.boleto_id, b.contrato_id, b.numero_documento, b.valor, b.vencimento, b.banco, b.status_boleto
                                          FROM tb_boletos b JOIN tb_contrato c ON b.contrato_id = c.contrato_id 
                                          WHERE c.empresa_id = $empresa_id AND b.status_boleto = 'Pendente'";

                               $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $resultado = $stmt->get_result();

                                if ($resultado->num_rows > 0): ?>

                                    <?php while ($boleto = $resultado->fetch_object()): ?>
                                        <tr>
                                            <td> <?= htmlspecialchars($boleto->boleto_id) ?> </td>
                                            <td> <?= htmlspecialchars($boleto->contrato_id) ?> </td>
                                            <td> <?= htmlspecialchars($boleto->numero_documento) ?></td>
                                            <td> <?= htmlspecialchars($boleto->valor) ?> </td>
                                            <td> <?= htmlspecialchars($boleto->vencimento) ?> </td>
                                            <td> <?= htmlspecialchars($boleto->banco) ?> </td>
                                            <td><a class='btn btn-primary'
                                                    href='index.php?page=gerarBoletoLoc&id=<?= $boleto->boleto_id ?>'>Gerar</a></td>
                                            <td><a class='btn btn-success'
                                                    href='index.php?page=enviarComprovante&id=<?= $boleto->boleto_id ?>'>Enviar</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">Nenhum boleto encontrado ou erro ao buscar dados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h2 style="margin-top: 40px;">Boletos Pagos</h2>
                    <div class="table-wrapper">
                        <table class="table table-striped-green text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contrato</th>
                                    <th>Número do Boleto</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Banco</th>
                                    <th>Status</th>
                                    <th>Data de Envio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT b.boleto_id, b.contrato_id, b.numero_documento, b.valor, b.vencimento, b.banco, b.status_boleto, cpr.data_envio 
                                          FROM tb_boletos b 
                                          JOIN tb_contrato c ON b.contrato_id = c.contrato_id 
                                          LEFT JOIN tb_comprovantes cpr ON b.boleto_id = cpr.boleto_id
                                          WHERE c.empresa_id = $empresa_id AND b.status_boleto = 'Enviado'";

                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $resultado = $stmt->get_result();

                                if ($resultado->num_rows > 0): ?>

                                <?php while ($boleto = $resultado->fetch_object()): 
                                    $data = $boleto->data_envio;
                                    $dataFormatada = date('d/m/Y H:i', strtotime($data));?>
                                    <tr>
                                     <td> <?= htmlspecialchars($boleto->boleto_id) ?></td>
                                     <td> <?= htmlspecialchars($boleto->contrato_id) ?></td>
                                     <td> <?= htmlspecialchars($boleto->numero_documento) ?></td>
                                     <td> <?= htmlspecialchars($boleto->valor) ?></td>
                                     <td> <?= htmlspecialchars($boleto->vencimento) ?></td>
                                     <td> <?= htmlspecialchars($boleto->banco) ?></td>
                                     <td> <?= htmlspecialchars($boleto->status_boleto) ?></td>
                                     <td> <?= htmlspecialchars($dataFormatada) ?></td>
                                     </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">Nenhum boleto encontrado ou erro ao buscar dados.</td>
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
