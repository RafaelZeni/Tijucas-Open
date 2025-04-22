const mapContainer = document.getElementById("map");
const l1Button = document.querySelector(".link-andar button:nth-child(1)");
const l2Button = document.querySelector(".link-andar button:nth-child(2)");

console.log("Botão L1:", l1Button);
console.log("Botão L2:", l2Button);

const imageUrlL1 = "conteudo_livre/assets/imgs/PISO_L1.jpg";
const imageUrlL2 = "conteudo_livre/assets/imgs/PISO_L2.jpg";

const imageWidth = 1920;
const imageHeight = 859;
const bounds = [
  [0, 0],
  [imageHeight, imageWidth],
];

const map = L.map("map", {
  crs: L.CRS.Simple,
  minZoom: -1,
  maxZoom: 2,
  zoomControl: true,
});

let currentLayer = L.imageOverlay(imageUrlL2, bounds).addTo(map);
map.fitBounds(bounds);

// Função para adicionar marcadores a um layer
function addMarkers(layer, markersData) {
  markersData.forEach((markerInfo) => {
    L.marker(markerInfo.coords).addTo(layer).bindPopup(markerInfo.popup);
  });
}

// Dados dos marcadores para o Piso L2
const markersL2Data = [
  { coords: [157, 385], popup: "<b>teste1</b><br>Comida deliciosa." },
  { coords: [157, 582], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [157, 785], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [157, 979], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [157, 1169], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [157, 1370], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [690, 1169], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [690, 979], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [690, 785], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [690, 582], popup: "<b>Restaurante</b><br>Comida deliciosa." },
  { coords: [690, 385], popup: "<b>Restaurante</b><br>Comida deliciosa." },
];

// Dados dos marcadores para o Piso L1 (adicione suas coordenadas e textos aqui)
const markersL1Data = [
  { coords: [720, 110], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [100, 110], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [720, 330], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [100, 330], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [720, 640], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [100, 640], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [100, 1070], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [720, 1070], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [100, 1410], popup: "<b>Loja A</b><br>Produtos incríveis!" },
  { coords: [720, 1410], popup: "<b>Serviço B</b><br>Ótimo atendimento." },
  // Adicione mais marcadores para o Piso L1
];

// Adiciona os marcadores iniciais (Piso L2)
const markersL2Layer = L.layerGroup();
addMarkers(markersL2Layer, markersL2Data);
markersL2Layer.addTo(map);

// Adiciona os marcadores do Piso L1 (inicialmente não adicionados ao mapa)
const markersL1Layer = L.layerGroup();
addMarkers(markersL1Layer, markersL1Data);

l1Button.addEventListener("click", function () {
  console.log("Botão L1 clicado!");
  map.removeLayer(currentLayer);
  map.removeLayer(markersL2Layer);
  currentLayer = L.imageOverlay(imageUrlL1, bounds).addTo(map);
  markersL1Layer.addTo(map);
  map.fitBounds(bounds);
});

l2Button.addEventListener("click", function () {
  console.log("Botão L2 clicado!");
  map.removeLayer(currentLayer);
  map.removeLayer(markersL1Layer);
  currentLayer = L.imageOverlay(imageUrlL2, bounds).addTo(map);
  markersL2Layer.addTo(map);
  map.fitBounds(bounds);
});
