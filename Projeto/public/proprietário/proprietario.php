<?php
session_start();

if (!isset($_SESSION['logins_id']) || $_SESSION['tipo_usu'] !== 'proprietario') {
    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
    exit();
}

require '../../app/database/connection.php';
$conn = conecta_db();

// ----------- Rosquinha - Espaços -------------------
$sql = "SELECT espaco_status, COUNT(*) AS total FROM tb_espacos GROUP BY espaco_status";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['espaco_status'];
        $data[] = (int)$row['total'];
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
    COUNT(DISTINCT CASE 
        WHEN c.data_inicio <= mes.m AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) > mes.m 
        THEN c.contrato_id 
    END) * 3000 AS faturamento
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
    ON mes.m BETWEEN c.data_inicio AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)
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
        $nome_mes = ucfirst(utf8_encode(strftime('%B', mktime(0,0,0,$row['mes_num'],1))));

        $meses[] = $nome_mes . '/' . $ano;
        $faturamentos[] = (float)$row['faturamento'];

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
  <h1>Bem-Vindo Cristiano</h1>

  <!-- Gráfico Rosquinha -->
  <div class="row">
    <h3 class="mb-3">Status dos Espaços</h3>
    <div class="col-md-6">
      <div style="width: 350px; height: 350px;">
        <canvas id="espacosChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Resumo Faturamento -->
  <div class="row mt-5">
    <h3 class="mb-3">Projeção de Faturamento Mensal (R$)</h3>
    <div class="col-md-12">

      <!-- Gráfico de linha -->
      <canvas id="faturamentoChart" style="max-width: 900px; max-height: 400px;"></canvas>
    </div>
    <!-- Botão para ver detalhes -->
    <div class="row mt-5">
      <div class="col-md-12 d-flex justify-content-end">
          <a href="index.php?page=projecao" class="btn btn-success">Ver Detalhes da Projeção</a>
      </div>
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
        backgroundColor: ['#3b5c2f', '#afcaa4'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: {
          display: true,
          text: 'Espaços Disponíveis vs Alugados'
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
        backgroundColor: 'rgba(59, 92, 47, 0.3)',
        borderColor: '#3b5c2f',
        tension: 0.3,
        pointRadius: 5,
        pointHoverRadius: 7,
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            }
          }
        }
      },
      plugins: {
        legend: { position: 'top' },
        title: {
          display: true,
          text: 'Projeção de Faturamento Mensal para <?php echo $currentYear . ' e ' . $nextYear; ?>'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
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
