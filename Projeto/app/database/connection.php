<?php
function conecta_db() {
  $db_name = "db_tijucas";
  $user = "dbtijucasopen";
  $pass = "TijUcasOpenBancodeDados";
  $server = "mysql742.umbler.com:41890";

  $conn = new mysqli($server, $user, $pass, $db_name);

  if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
  }

  // Garante que a comunicação seja em UTF-8 (opcional mas recomendado)
  $conn->set_charset("utf8");

  return $conn;
}
?>
