<!--Função que permite o usuário, após logado como Proprietário 
ou Locatário, sair de seu perfil e voltar para a página de Login-->

<?php
session_start();
session_unset(); 
session_destroy(); 

header("Location: index.php?page=entrar"); 
exit;
?>