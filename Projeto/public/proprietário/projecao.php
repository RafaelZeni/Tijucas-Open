<!--Esta página permite ao proprietário vizualizar os contratos em dia, 
mostrando quanto dineiro o proprietário fará por mês de acordo com os contratos-->

<?php
require '../../app/database/connection.php';

$conn = conecta_db();

$currentYear = date('Y');

// Gera os meses de jan a dez do ano atual e do seguinte
$sql = "
SELECT
    DATE_FORMAT(mes.m, '%Y-%m') AS mes_ano,
    MONTH(mes.m) AS mes_num,
    YEAR(mes.m) AS ano,
    DATE_FORMAT(mes.m, '%M') AS mes_nome,
    COUNT(DISTINCT CASE WHEN c.data_inicio <= mes.m AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) > mes.m THEN c.contrato_id END) AS contratos_ativos,
    COUNT(DISTINCT CASE WHEN MONTH(DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)) = MONTH(mes.m) AND YEAR(DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)) = YEAR(mes.m) THEN c.contrato_id END) AS contratos_vencem,
    COUNT(DISTINCT CASE WHEN c.data_inicio <= mes.m AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR) > mes.m THEN c.contrato_id END) * 3000 AS faturamento
FROM (
    SELECT DATE_FORMAT(DATE_ADD(MAKEDATE($currentYear, 1), INTERVAL n MONTH), '%Y-%m-01') AS m
    FROM (
        SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
        UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
        UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17
        UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23
    ) AS meses
) AS mes
LEFT JOIN tb_contrato c ON mes.m BETWEEN c.data_inicio AND DATE_ADD(c.data_inicio, INTERVAL 1 YEAR)
GROUP BY mes.m
ORDER BY mes.m;
";

$result = $conn->query($sql);
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
    <div class="container">
    <h2 class="mb-4">Projeção de Faturamento Mensal</h2>

    <a href="proprietario.php" class="btn btn-dark mb-3">Voltar</a>

    <table class="table table-striped-green text-center">
      <thead class="table-success">
        <tr>
          <th>Mês</th>
          <th>Ano</th>
          <th>Contratos Ativos</th>
          <th>Contratos que Vencem</th>
          <th>Faturamento Projetado</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            $meses_pt = [
              1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
              5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
              9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $meses_pt[(int)$row['mes_num']] . "</td>";
                echo "<td>{$row['ano']}</td>";
                echo "<td>{$row['contratos_ativos']}</td>";
                echo "<td>{$row['contratos_vencem']}</td>";
                echo "<td>R$ " . number_format($row['faturamento'], 2, ',', '.') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nenhum dado encontrado.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>


</body>
</html>
