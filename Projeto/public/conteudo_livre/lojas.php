<!--Página de lojas, local que aparece todas as lojas após seu
cadastro final (Cadastro Locatário, Cadastro Contrato, Cadastro loja).
 Exibe uma imagem da Loja em questão e exibe suas informações-->

<?php
require __DIR__ . '/../../app/database/connection.php';
$conn = conecta_db(); // agora $conn estará disponível para tudo

// Recebe o filtro vindo do formulário
$filtro = isset($_POST['filtro_loja']) ? trim($_POST['filtro_loja']) : '';

// Verifica se há um filtro selecionado, e muda a consulta ao sql
if ($filtro != '') {
    $stmt = $conn->prepare("SELECT * FROM tb_lojas WHERE loja_tipo = ? LIMIT 20");
    $stmt->bind_param("s", $filtro);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Se não tiver filtro, mostra todas as lojas
    $sql = "SELECT * FROM tb_lojas LIMIT 20";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="lojas">
    
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerCachoeiraBorrado.jpg" alt="Estacionamento Tijucas Open">
        <div class="banner-conteudo">
            <h2>Visite nossas lojas abaixo:</h2>
            <a href="#filtro" class="btn-visitar">Visitar lojas</a>
    </div>
    </section>

    <section class="lojass" id="filtro">
        <h2>Nossas Lojas:</h2>
        <label>Filtros</label>
        <form id="filtro" method="POST">
            <select class="filtro" name="filtro_loja" onchange="this.form.submit()">
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
                    echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3 loja">';

                    echo '<div class="card-wrap">';

                    echo '<div class="card-face card-front">';
                    // Verifica se a logo está presente e exibe
                    if (!empty($row["loja_logo"])) {
                        echo '<img src="' . htmlspecialchars($row["loja_logo"]) . '" alt="Logo da loja">';
                    } else {
                        // Caso não haja logo, exibe uma imagem de fallback
                        echo '<img src="conteudo_livre/assets/imgs/logo-placeholder.png" alt="Logo de placeholder">';
                    }
                    
                    echo '<div class="card-body">';
                    // Exibe o nome da loja
                    echo '<h5 class="card-title">' . htmlspecialchars($row["loja_nome"]) . '</h5>';
                    echo '</div>'; // card-body
                    echo '</div>'; // card front

                    echo '<div class="card-face card-back">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["loja_nome"]) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row["loja_andar"]) . '</p>';
                    echo '<p class="card-text">' . htmlspecialchars($row["loja_telefone"]) . '</p>';
                    echo '</div>'; // card-body
                    echo '</div>'; // card-back

                    echo '</div>'; // card-wrapper
                    echo '</div>'; // loja
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
