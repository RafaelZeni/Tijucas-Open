</body>
</html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil do Proprietário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body, html {
      height: 100%;
    }

    .header {
      width: 100%;
      height: 100px;
      background-color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: relative;
      z-index: 2;
    }

    .header .logo {
      height: 50px;
    }

    .header .user-area {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .header .user-button {
      background-color: #455b34;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
    }

    .container {
      display: flex;
      height: calc(100vh - 70px); /* subtrai a altura do header */
    }

    .left-side {
      flex: 1;
      background-image: url('../conteudo_livre/assets/imgs/maisde40espaços.jpeg'); /* imagem de fundo */
      background-size: cover;
      background-position: center;
    }

    .right-side {
      flex: 1;
      background-color: #ffffff;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .avatar {
      width: 180px;
      height: 180px;
      background-color: #e6e1ea;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 80px;
      color: #3d2b52;
      margin-bottom: 20px;
    }

    .nome-usuario {
      background-color: #e6e1ea;
      padding: 10px 20px;
      font-size: 20px;
      border-bottom: 2px solid #5c5c5c;
      margin-bottom: 30px;
    }

    .btn {
      display: block;
      padding: 15px 25px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      color: white;
      margin-bottom: 20px;
      width: 260px;
      cursor: pointer;
    }

    .btn-dark-green {
      background-color: #455b34;
    }

    .btn-light-green {
      background-color: #72985e;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
      }

      .left-side,
      .right-side {
        flex: none;
        width: 100%;
        height: 50vh;
      }

      .right-side {
        height: auto;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <img src="../conteudo_livre/assets/imgs/logo.jpeg" alt="Logo Tijucas Open" class="logo">
    <div class="user-area">
      <button class="user-button">Cristiano</button>
      <span>👤</span>
    </div>
  </header>

  <div class="container">
    <div class="left-side"></div>

    <div class="right-side">
      <div class="avatar">👤</div>

      <div class="nome-usuario">Cristiano Ramos de Lima</div>

      <a href="index.php?page=gerenciarLocatarios" class="btn btn-primary mb-3">Gerenciar Locatarios</a>
      <a href="index.php?page=gerenciarContratos" class="btn btn-success mb-3">Gerenciar Contratos</a>
      <a href="index.php?page=acessarRelatorios" class="btn btn-danger mb-3">Acessar Relatórios</a>
    </div>
  </div>
</body>
</html>