<?php

require '../../app/database/connection.php';
$conn = conecta_db();

// Aqui supondo que o id do usuário logado está numa variável (exemplo fixo para demo)
$user_id = 1; // Substitua pelo id real do usuário logado, normalmente da sessão

// Busca o auth_secret do usuário para saber se já ativou 2FA
$sql_auth = "SELECT auth_secret FROM tb_logins WHERE logins_id = ?";
$stmt_auth = $conn->prepare($sql_auth);
$stmt_auth->bind_param("i", $user_id);
$stmt_auth->execute();
$result_auth = $stmt_auth->get_result();
$auth_secret = null;
if ($result_auth->num_rows > 0) {
    $row_auth = $result_auth->fetch_assoc();
    $auth_secret = $row_auth['auth_secret'];
}
$stmt_auth->close();

// ----------- Rosquinha - Espaços -------------------
$sql = "SELECT espaco_status, COUNT(*) AS total FROM tb_espacos GROUP BY espaco_status";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['espaco_status'];
        $data[] = (int) $row['total'];
    }
}

// ----------- Faturamento Mensal + Resumo -----------
$currentYear = date('Y');
$nextYear = $currentYear + 1;

$sql_faturamento = "
SELECT
    DATE_FORMAT(mes.m, '%Y-%m') AS mes_ano,
    MONTH(mes.m) AS mes_num,
    DATE_FORMAT(mes.m, '%M') AS mes_nome,
    COUNT(DISTINCT CASE 
        WHEN c.data_inicio <= mes.m AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) > mes.m 
        THEN c.contrato_id 
    END) AS contratos_ativos,
    COUNT(DISTINCT CASE 
        WHEN MONTH(DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)) = MONTH(mes.m)
          AND YEAR(DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)) = YEAR(mes.m) 
        THEN c.contrato_id 
    END) AS contratos_vencem,
    SUM(CASE 
        WHEN c.data_inicio <= mes.m AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) > mes.m 
        THEN c.valor_mensal 
        ELSE 0 
    END) AS faturamento
FROM (
    SELECT DATE_ADD(MAKEDATE(YEAR(CURDATE()), 1), INTERVAL n MONTH) AS m
    FROM (
        SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
        UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
        UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17
        UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23
    ) AS meses
) AS mes
LEFT JOIN tb_contrato c 
    ON mes.m BETWEEN c.data_inicio AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) AND c.contrato_status = 'Ativo'
GROUP BY mes.m
ORDER BY mes.m;
";

$result_faturamento = $conn->query($sql_faturamento);

$meses = [];
$faturamentos = [];
$resumo_mensal = [];
$total_ano_atual = 0;
$total_ano_proximo = 0;

if ($result_faturamento->num_rows > 0) {
    while ($row = $result_faturamento->fetch_assoc()) {
        $ano = substr($row['mes_ano'], 0, 4);
        setlocale(LC_TIME, 'pt_BR.UTF-8');
        $nome_mes = ucfirst(utf8_encode(strftime('%B', mktime(0, 0, 0, $row['mes_num'], 1))));

        $meses[] = $nome_mes . '/' . $ano;
        $faturamentos[] = (float) $row['faturamento'];

        $resumo_mensal[] = [
            'mes' => $nome_mes,
            'ano' => $ano,
            'ativos' => $row['contratos_ativos'],
            'vencem' => $row['contratos_vencem'],
            'faturamento' => $row['faturamento']
        ];

        if ($ano == $currentYear) {
            $total_ano_atual += $row['faturamento'];
        } elseif ($ano == $nextYear) {
            $total_ano_proximo += $row['faturamento'];
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Perfil do Proprietário</title>
  <link rel="stylesheet" href="proprietario.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Estilos gerais para o conteúdo principal */
    .content {
        margin-left: 250px; /* Margem esquerda para compensar a sidebar */
        padding: 20px;
        flex-grow: 1; /* Ocupa o restante do espaço horizontal */
        display: flex;
        flex-direction: column;
        align-items: center; /* Centraliza o conteúdo horizontalmente */
        justify-content: flex-start; /* Alinha o conteúdo ao topo */
        min-height: 100vh;
        background-color: #f8f9fa; /* Um cinza claro para o fundo */
    }

    .content h1 {
        color: #2e4d41; /* Cor do título principal */
        margin-bottom: 30px;
        font-weight: 600;
    }

    /* Estilos para os cards de gráficos */
    .card-chart {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 5px; /* Espaçamento entre os cards */
        width: 100%; /* Ocupa a largura total da coluna */
        max-width: 1000px; /* Largura máxima para os cards */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .card-chart h3 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 500;
        text-align: center;
    }

    /* Estilos para os canvas dos gráficos */
    canvas {
        max-width: 100%; /* Garante que o gráfico seja responsivo */
        height: auto; /* Mantém a proporção */
    }

    /* Estilo para o botão de detalhes */
    .btn-success {
        background-color: #4e7d69; /* Um verde mais claro para o botão */
        border-color: #4e7d69;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1em;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background-color: #3b5c2f; /* Um verde mais escuro no hover */
        border-color: #3b5c2f;
    }

    /* Ajustes para o layout de colunas do Bootstrap */
    .row.mt-2, .row.mt-5 {
        width: 100%; /* Garante que as linhas ocupem toda a largura do content */
        justify-content: center; /* Centraliza as colunas dentro das linhas */
    }
    .col-md-6, .col-md-12 {
        display: flex;
        justify-content: center; /* Centraliza o conteúdo dentro da coluna */
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <div class="logo">
      <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
    </div>

    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    ?>

    <nav>
      <a href="index.php" class="<?= ($page == 'home') ? 'ativo' : ''; ?>">Início</a>
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
    <h1>Bem-Vindo Cristiano</h1>
    <?php if (empty($auth_secret)) : ?>
      <a href="index.php?page=ativar2fa" class="btn btn-primary mb-3">Ativar 2FA</a>
    <?php else : ?>
        <span class="btn btn-success mb-3" style="cursor: default;">2FA ativa</span>
    <?php endif; ?>

    <div class="row mt-2">
      <div class="col-md-12"> <div class="card-chart">
          <h3>Status dos Espaços</h3>
          <div class="d-flex justify-content-center mb-4"> <a href="index.php?page=gerenciarEspacos" class="btn btn-success">Gerenciar Espaços</a>
          </div>
          <div style="width: 300px; height: 300px;"> <canvas id="espacosChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-md-12"> <div class="card-chart">
          <h3>Projeção de Faturamento Mensal (R$)</h3>
          
          <div class="d-flex justify-content-center mb-4"> <a href="index.php?page=projecao" class="btn btn-success">Ver Detalhes da Projeção</a>
          </div>

          <canvas id="faturamentoChart" style="max-width: 1000px; max-height: 400px;"></canvas> </div>
      </div>
    </div>
  </div>

  <script>
    // Rosquinha - Espaços
    const ctx = document.getElementById('espacosChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Espaços',
          data: <?php echo json_encode($data); ?>,
          backgroundColor: ['#3b5c2f', '#afcaa4'], // Cores harmonizadas com o tema verde
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // Permite maior controle sobre o tamanho
        plugins: {
          legend: { position: 'bottom' },
          title: {
            display: true,
            text: 'Espaços Disponíveis vs Alugados',
            font: {
                size: 16,
                weight: 'bold'
            }
          }
        }
      }
    });

    // Linha - Faturamento
    const ctx2 = document.getElementById('faturamentoChart').getContext('2d');
    new Chart(ctx2, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($meses); ?>,
        datasets: [{
          label: 'Faturamento (R$)',
          data: <?php echo json_encode($faturamentos); ?>,
          fill: true,
          backgroundColor: 'rgba(59, 92, 47, 0.3)', // Cor de preenchimento harmonizada
          borderColor: '#3b5c2f', // Cor da linha harmonizada
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // Permite maior controle sobre o tamanho
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: 'Projeção de Faturamento Mensal para <?php echo $currentYear . ' e ' . $nextYear; ?>',
            font: {
                size: 16,
                weight: 'bold'
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
              }
            }
          }
        }
      }
    });
  </script>
</body>

</html>
