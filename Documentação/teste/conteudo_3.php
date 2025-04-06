<?php
	if(isset($_POST['descricao'])){
		$obj = conecta_db();
		$query = "UPDATE tb_teste
		SET descricao = '".$_POST['descricao']."'
		WHERE teste_id = '".$_GET['id']."'";
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
				<h2> CRUD - UPDATE - Alterando o ID: 
				<?php echo $_GET['id'];?> </h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
			
			<form 
			method="POST" 
			action="index.php?page=3&id=<?php echo $_GET['id'];?>">
			
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
