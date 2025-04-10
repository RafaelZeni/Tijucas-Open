<section class="mapa">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: white;
      overflow-y: auto;
    }

    .mapa {
      position: relative;
      padding-top: 120px; /* espaço para o header fixo */
      padding-bottom: 100px; /* espaço para o footer */
      min-height: 100vh;
    }

    .link-andar {
      margin-bottom: 20px;
      text-align: center;
    }

    #botaoregistro {
      padding: 10px 20px;
      background-color: #3b5c2f;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin: 5px;
    }

    .iactiveImg {
      margin: 0 auto;
      max-width: 100%;
      text-align: center;
    }
  </style>

  <div class="link-andar">
    <button id="botaoregistro" type="button">L1</button>
    <button id="botaoregistro" type="button">L2</button>
  </div>

  <div class="iactiveImg" data-ii="65454"></div>
  <script src="https://interactive-img.com/js/include.js"></script>
</section>