<?php
    require '../../app/database/connection.php';

    if (isset($_GET['id'])) {
        $empresa_id = $_GET['id'];

        $conn = conecta_db();

        $query = "CALL pr_RemoverLocatario(?)";

        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            echo "Erro ao preparar a consulta.";
            exit;
        }

        $stmt->bind_param("i", $empresa_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Locatário removido com sucesso!');
                    window.location.href = 'gerenciarLocatarios.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Erro ao remover o locatário: " . $stmt->error . "');
                  </script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>
                alert('ID do locatário não fornecido.');
                window.location.href = 'gerenciarLocatarios.php';
              </script>";
    }
?>
