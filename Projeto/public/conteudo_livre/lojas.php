<?php
require __DIR__ . '/../../app/database/connection.php';
$obj = conecta_db();// agora $conn estará disponível para tudo


$sql = "SELECT loja_nome, loja_logo FROM tb_lojas LIMIT 20";
$result = $obj->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="lojas">
    
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerEstacionamento.png" alt="Estacionamento Tijucas Open">
    </section>

    <section class="lojass">
        <h2>Nossas Lojas:</h2>
        <div class="grid-lojas">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="loja">';
                    
                    // Verifica se a logo está presente e exibe
                    if (!empty($row["loja_logo"])) {
                        echo '<img src="' . htmlspecialchars($row["loja_logo"]) . '" alt="Logo da loja">';
                    } else {
                        // Caso não haja logo, exibe uma imagem de fallback
                        echo '<img src="conteudo_livre/assets/imgs/logo-placeholder.png" alt="Logo de placeholder">';
                    }

                    // Exibe o nome da loja
                    echo '<p>' . htmlspecialchars($row["loja_nome"]) . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p>Nenhuma loja cadastrada.</p>";
            }
            ?>
        </div>
    </section>

</body>
</html>
