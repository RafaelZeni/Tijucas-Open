<?php
session_start(); // ESSENCIAL: Iniciar a sessão no topo do script principal

// Verifica se o usuário é locatário e está logado
if (!isset($_SESSION['logins_id']) || !isset($_SESSION['tipo_usu']) || $_SESSION['tipo_usu'] !== 'locatario') {
    // Caminho absoluto para o redirecionamento é mais robusto
    // Ajuste '/Tijucas-Open/Projeto/public/index.php?page=entrar' se necessário
    $baseUrl = "/Tijucas-Open/Projeto/public/index.php"; // Defina seu baseUrl
    header("Location: " . $baseUrl . "?page=entrar");
    exit();
}

// Define a página atual para uso na navegação dos arquivos incluídos
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';

if(isset($_GET['page'])) {
    if ($_GET['page'] == 'gestaoContratos') {
        include 'gestaoContratos.php';
    } else if ($_GET['page'] == 'visualizarEspacos') {
        include 'visualizarEspacos.php';
    } else if ($_GET['page'] == 'ativar2fa') {
        include '../conteudo_livre/autenticacao/ativar_2fa.php';
    } else if ($_GET['page'] == 'verificar2fa') {
        include '../conteudo_livre/autenticacao/verificar_2fa.php';
    } else if ($_GET['page'] == 'gerarBoletoLoc') {
        include 'gerarBoletoLoc.php';
    } else if ($_GET['page'] == 'home') { // Adicionar condição explícita para home
        include 'locatario.php'; // Assumindo que locatario.php é sua página inicial do painel
    }
    else {
        // Redireciona para a página inicial do painel se a página não for reconhecida
        // ou pode mostrar uma página de erro 404.
        // Se locatario.php é a home, o redirecionamento abaixo é para ela.
        header('Location: index.php'); // Redireciona para a index principal (que vai carregar locatario.php por padrão)
    }
} else {
    // Se nenhuma página for especificada, carrega a página inicial do painel
    include 'locatario.php'; // Assumindo que locatario.php é sua página inicial
}
?>