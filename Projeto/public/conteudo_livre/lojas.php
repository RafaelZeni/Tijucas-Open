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
    <link rel="stylesheet" href="conteudo_livre/assets/css/lojas.css">
    <style>
        /* Container para o grid de lojas */
        .grid-lojas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* Ajustando a coluna */
            gap: 2rem;
            padding: 2rem;
            justify-items: center;
        }

        /* Estilo individual das lojas */
        .loja {
            width: 100%;
            height: 180px;
            background-color: #f4f4f4;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            text-align: center;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para a imagem da loja (logo) */
        .loja img {
            width: 80px;
            height: 80px;
            object-fit: contain; /* Garante que a imagem será redimensionada corretamente */
            margin-bottom: 1rem;
        }

        /* Estilo para o texto do nome da loja */
        .loja p {
            font-weight: bold;
            margin-top: 0.5rem;
            color: #333;
        }

        /* Estilo para o banner */
        .banner img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerEstacionamento.png" alt="Estacionamento Tijucas Open">
    </section>

    <section class="lojas">
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
