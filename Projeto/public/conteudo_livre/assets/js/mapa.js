
const imageUrl = "conteudo_livre/assets/imgs/PISO_L2.jpg";

const imageWidth = 1920;
const imageHeight = 859;

const bounds = [[0, 0], [imageHeight, imageWidth]];


const map = L.map('map', {
    crs: L.CRS.Simple,
    minZoom: -1,
    maxZoom: 2,
    zoomControl: true
});

L.imageOverlay(imageUrl, bounds).addTo(map);
map.fitBounds(bounds);

L.marker([157, 385]).addTo(map)
  .bindPopup("<b>teste1</b><br>Comida deliciosa.");

L.marker([157, 582]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([157, 785]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([157, 979]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([157, 1169]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([157, 1370]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([690, 1169]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([690, 979]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([690, 785]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([690, 582]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

L.marker([690, 385]).addTo(map)
  .bindPopup("<b>Restaurante</b><br>Comida deliciosa.");

