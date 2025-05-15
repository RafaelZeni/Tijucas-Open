<?php
    require '../../app/database/connection.php';

    if (isset($_GET['id'])) {
        $empresa_id = $_GET['id'];

        $conn = conecta_db();

        $query = "CALL pr_RemoverLoja(?)";

        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            echo "Erro ao preparar a consulta.";
            exit;
        }

        $stmt->bind_param("i", $empresa_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Loja removida com sucesso!');
                    window.location.href = 'index.php?page=gerenciarLojas';
                  </script>";
        } else {
            echo "<script>
                    alert('Erro ao remover o loja: " . $stmt->error . "');
                  </script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>
                alert('ID da loja não fornecido.');
                window.location.href = 'index.php?page=gerenciarLojas';
              </script>";
    }
?>
