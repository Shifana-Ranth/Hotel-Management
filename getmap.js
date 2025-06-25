const greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const hotelId = document.getElementById("hotel-id").value;
const coords = hotelCoordinates[hotelId];
if (coords) {
    const map = L.map('map').setView(coords, 13);
    console.log("goood to go");
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker(coords, { icon: greenIcon }).addTo(map)
    .bindPopup('Hotel')
    .openPopup();
} 
else {
    alert("Hotel coordinates not found!");
}