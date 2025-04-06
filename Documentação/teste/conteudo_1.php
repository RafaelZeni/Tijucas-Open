<?php
	if(isset($_POST['descricao'])){
		$obj = conecta_db();
		$query = "INSERT INTO 
					tb_teste(descricao) 
					VALUES ('".$_POST['descricao']."')";
		$resultado = $obj->query($query);
		if($resultado){
			header("location: index.php");
		}else{
			echo "<span class='alert alert-danger'>Não funcionou!</span>";
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Meu primeiro CRUD</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2> CRUD - Insert </h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
			
			<form method="POST" action="index.php?page=1">
			<input type="text"
					name="descricao"
					class="form-control"
					placeholder="Digite sua descrição aqui.">
			<button type="submit" 
					class="mt-2 btn btn-primary">Enviar</button>
			
			</form>
			</div>
		</div>
		
		
	</div>
</body>
</html>
