<?php
/* ANÁLISE NECESSÁRIA */



















    require '../../app/database/connection.php';

    if (isset($_GET['id'])){
        $contrato_id = $_GET['id'];

        $obj = conecta_db();

        $query = "CALL pr_RemoverContrato(?)";

        $stmt = $obj->prepare($query);

        if ($stmt === false) {
            echo "Erro ao preparar consulta: " . $obj->error;
            exit;
        }

        
        $stmt->bind_param("i", $contrato_id);

        if ($stmt->execute()) {
            echo "<script>
                        alert('Contrato removido com sucesso!');
                        window.location.href = 'index.php?page=gerenciarContratos';
                   </script>";
        } else {
            echo "<script>
                        alert('Erro ao remover Contrato!". $stmt->error ."');
                   </script>";
        }

        $stmt->close();
        $obj->close();

    } else {
        echo "<script>
                    alert('ID do contrato não fornecido!');
                    window.location.href = 'index.php?page=gerenciarContratos';
               </script>";
    }
?>