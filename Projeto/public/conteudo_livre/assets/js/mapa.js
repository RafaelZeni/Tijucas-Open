const mapContainer = document.getElementById("map");
const l1Button = document.querySelector(".link-andar button:nth-child(1)");
const l2Button = document.querySelector(".link-andar button:nth-child(2)");

const imageUrlL1 = "conteudo_livre/assets/imgs/PISO1.jpg";
const imageUrlL2 = "conteudo_livre/assets/imgs/PISO2.jpg";

const imageWidth = 1920;
const imageHeight = 1080;
const bounds = [
  [0, 0],
  [imageHeight, imageWidth],
];

// Inicialização do mapa
const map = L.map("map", {
  crs: L.CRS.Simple,
  minZoom: -3,
  maxZoom: 0,
  zoomControl: false,
  dragging: false,
  scrollWheelZoom: false,
  doubleClickZoom: false,
});

// Define camada inicial como L2
let currentLayer = L.imageOverlay(imageUrlL2, bounds).addTo(map);
map.fitBounds(bounds);

// LayerGroups para cada andar
const markersL1Layer = L.layerGroup();
const markersL2Layer = L.layerGroup();

// Coordenadas fixas por espaco_id
const coordsPorEspaco = {
  // L1: espaco_id 1–10
  1: [910, 180],
  2: [910, 350],
  3: [910, 520],
  4: [910, 690],
  5: [910, 860],
  6: [910, 1020],
  7: [100, 180],
  8: [100, 350],
  9: [100, 520],
  10: [100, 690],
  11: [100,860],
  12: [100,1020],

  // L2: espaco_id 11–22
  13: [910, 180],
  14: [910, 350],
  15: [910, 520],
  16: [910, 690],
  17: [910, 860],
  18: [910, 1020],
  19: [100, 180],
  20: [100, 350],
  21: [100, 520],
  22:  [100, 690],
  23:  [100,860],
  24:  [100,1020],
};

// Carrega dados das lojas do PHP
fetch("conteudo_livre/get_lojas.php")
  .then((response) => response.json())
  .then((lojas) => {
    lojas.forEach((loja) => {
      const coords = coordsPorEspaco[loja.espaco_id];
      if (!coords) return;

      const popup = `<b>${loja.nome}</b><br>Telefone: ${loja.telefone}<br>Tipo: ${loja.tipo}`;
      const marker = L.marker(coords).bindPopup(popup);

      if (loja.andar === "L1") {
        marker.addTo(markersL1Layer);
      } else if (loja.andar === "L2") {
        marker.addTo(markersL2Layer);
      }
    });

    // Adiciona L2 por padrão ao carregar
    markersL2Layer.addTo(map);
  });

// Alternar para L1
l1Button.addEventListener("click", function () {
  map.removeLayer(currentLayer);
  map.removeLayer(markersL2Layer);
  currentLayer = L.imageOverlay(imageUrlL1, bounds).addTo(map);
  markersL1Layer.addTo(map);
  map.fitBounds(bounds);
});

// Alternar para L2
l2Button.addEventListener("click", function () {
  map.removeLayer(currentLayer);
  map.removeLayer(markersL1Layer);
  currentLayer = L.imageOverlay(imageUrlL2, bounds).addTo(map);
  markersL2Layer.addTo(map);
  map.fitBounds(bounds);
});
