<?php
function conecta_db() {
  $db_name = "db_tijucas";
  $user = "dbtijucasopen";
  $pass = "TijUcasOpenBancodeDados";
  $server = "mysql742.umbler.com:41890";

  $conexao = new mysqli($server, $user, $pass, $db_name);

  if ($conexao->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conexao->connect_error);
  }

  // Garante que a comunicação seja em UTF-8 (opcional mas recomendado)
  $conexao->set_charset("utf8");

  return $conexao;
}
?>
