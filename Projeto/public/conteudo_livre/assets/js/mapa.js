const mapContainer = document.getElementById("map");
const l1Button = document.querySelector(".link-andar button:nth-child(1)");
const l2Button = document.querySelector(".link-andar button:nth-child(2)");

const imageUrlL1 = "conteudo_livre/assets/imgs/PISO_L1.jpg";
const imageUrlL2 = "conteudo_livre/assets/imgs/PISO_L2.jpg";

const imageWidth = 1920;
const imageHeight = 859;
const bounds = [
  [0, 0],
  [imageHeight, imageWidth],
];

// Inicialização do mapa
const map = L.map("map", {
  crs: L.CRS.Simple,
  minZoom: -1,
  maxZoom: 2,
  zoomControl: true,
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
  1: [720, 110],
  2: [100, 110],
  3: [720, 330],
  4: [100, 330],
  5: [720, 640],
  6: [100, 640],
  7: [100, 1070],
  8: [720, 1070],
  9: [100, 1410],
  10: [720, 1410],
  11: [],
  12: [],

  // L2: espaco_id 11–22
  13: [157, 385],
  14: [157, 582],
  15: [157, 785],
  16: [157, 979],
  17: [157, 1169],
  18: [157, 1370],
  19: [690, 1169],
  20: [690, 979],
  21: [690, 785],
  22: [690, 582],
  23: [690, 385],
  24: [] 
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
