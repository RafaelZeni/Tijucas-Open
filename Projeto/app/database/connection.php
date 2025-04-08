<?php
//Dados conexão
// $servername = "mysql247.umbler.com:41890";
// $username = "synergy_school";
// $password = "BDSynergy";
// $dbname = "synergy_db";
function conecta_db() {
  $db_name = "db_tijucas"; //db_tijucas //db_tijucasopen
  $user = "root"; // tijucasopendb //dbtijucasopen
  $pass = ""; // TijUcasOpenBancodeDados 
  $server = "localhost:3307"; // 
  
  $conexao = new mysqli($server, $user, $pass, $db_name);

  return $conexao;

}
?>