<?php
function conecta_db() {
  $db_name = "db_tijucas"; //db_tijucas
  $user = "dbtijucasopen"; //dbtijucasopen
  $pass = "TijUcasOpenBancodeDados"; // TijUcasOpenBancodeDados 
  $server = "mysql742.umbler.com:41890"; // 
  
  $conexao = new mysqli($server, $user, $pass, $db_name);

  return $conexao;

}
?>