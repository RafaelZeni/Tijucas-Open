<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Boletos</title>
    <link rel="stylesheet" href="proprietario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../conteudo_livre/assets/imgs/LogoTijucasBranca.png" alt="Tijucas Open" />
        </div>

        <nav>
            <a href="index.php">Início</a>
            <a href="index.php?page=gerenciarLocatarios">Gerenciar Locatários</a>
            <a href="index.php?page=gerenciarContratos" class="ativo">Gerenciar Contratos</a>
            <a href="index.php?page=gerenciarLojas">Gerenciar Lojas</a>
            <a href="index.php?page=gerenciarEspacos">Gerenciar Espaços</a>
        </nav>

        <div class="logout">
            <a href="../logout.php"><span>↩</span> Log Out</a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h1>Boletos do Contrato</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <a href="index.php" class="btn btn-dark mb-3">Voltar</a>
                    <div class="table-wrapper">
                        <table class="table table-striped-green text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Documento</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Pagador</th>
                                    <th>Banco</th>
                                    <th>Linha Digitável</th>
                                    <th>Gerar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../../app/database/connection.php';

                                $contrato_id = $_GET['id'];
                                $conn = conecta_db();
                                $query = "SELECT b.boleto_id, b.numero_documento, b.valor, b.banco, b.linha_digitavel, b.vencimento, l.empresa_nome FROM tb_boletos b JOIN tb_contrato c ON b.contrato_id = c.contrato_id JOIN tb_locatarios l ON l.empresa_id = c.empresa_id WHERE b.contrato_id = ?";

                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $contrato_id);
                                $stmt->execute();
                                $resultado = $stmt->get_result();

                                while ($boleto = $resultado->fetch_object()) {
                                    $vencimentoFormatado = new DateTime($boleto->vencimento);
                                    $html = "<tr>";
                                    $html .= "<td>" . $boleto->boleto_id . "</td>";
                                    $html .= "<td>" . $boleto->numero_documento . "</td>";
                                    $html .= "<td>" . $boleto->valor . "</td>";
                                    $html .= "<td>" . $vencimentoFormatado->format('d/m/Y') . "</td>";
                                    $html .= "<td>" . $boleto->empresa_nome . "</td>";
                                    $html .= "<td>" . $boleto->banco . "</td>";
                                    $html .= "<td>" . $boleto->linha_digitavel . "</td>";
                                    $html .= "<td><a class='btn btn-primary' href='index.php?page=gerarBoletoProp&id={$boleto->boleto_id}'>Gerar</a></td>";
                                    $html .= "</tr>";
                                    echo $html;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>