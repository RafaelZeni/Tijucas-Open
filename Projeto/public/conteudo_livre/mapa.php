<!-- Página que exibe o Mapa Interativo, com a intenção de ajudar 
o usuário a se localizar corretamente dentro do espaço do Tijucas Open-->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="mapas">
      <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerCachoeiraBorrado.jpg" alt="Estacionamento Tijucas Open">
    </section>
<section class="mapa">
  <div class="link-andar">
    <button type="button">L1</button>
    <button type="button">L2</button>
  </div>
  <div id="map" class="mapa-container"></div>
</section>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> 
<script src="conteudo_livre/assets/js/mapa.js"></script>
</body>
</html>
