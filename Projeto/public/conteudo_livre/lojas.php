<?php
require __DIR__ . '/../../app/database/connection.php';
$obj = conecta_db(); // agora $conn estará disponível para tudo

// Recebe o filtro vindo do formulário
$filtro = isset($_POST['filtro_loja']) ? trim($_POST['filtro_loja']) : '';

// Verifica se há um filtro selecionado, e muda a consulta ao sql
if ($filtro != '') {
    $stmt = $obj->prepare("SELECT loja_nome, loja_logo FROM tb_lojas WHERE loja_tipo = ? LIMIT 20");
    $stmt->bind_param("s", $filtro);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Se não tiver filtro, mostra todas as lojas
    $sql = "SELECT loja_nome, loja_logo FROM tb_lojas LIMIT 20";
    $result = $obj->query($sql);
}
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
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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
            object-fit: contain;
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
        <label>Filtros</label>
        <form id="filtro" method="POST">
            <select name="filtro_loja" onchange="this.form.submit()">
                <option value="">Todas as lojas</option>
                <option value="Roupas" <?= ($filtro == 'Roupas') ? 'selected' : '' ?>>Roupas</option>
                <option value="Esportes" <?= ($filtro == 'Esportes') ? 'selected' : '' ?>>Esportes</option>
                <option value="Alimentação" <?= ($filtro == 'Alimentação') ? 'selected' : '' ?>>Alimentação</option>
                <option value="Livros" <?= ($filtro == 'Livros') ? 'selected' : '' ?>>Livros</option>
                <option value="Jóias" <?= ($filtro == 'Jóias') ? 'selected' : '' ?>>Jóias</option>
            </select>
        </form>


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
                echo "<p>Nenhuma loja encontrada.</p>";
            }
            ?>
        </div>
    </section>

    <script src="conteudo_livre/assets/js/pesquisa.js"></script>
</body>

</html>
