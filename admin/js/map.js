/**
 *
 * @param id
 * @param coordinates
 * @param {object} options
 * @param options.centerIcon
 * @param options.onclick
 * @param options.onclickPopup
 */
function createMap(id, coordinates, options) {
    if(!options) {
        options = {};
    }
    const mapOptions = {
        center: [coordinates.latitude, coordinates.longitude],
        zoom: 14
    }

    const map = new L.map(id ||'map', mapOptions);

    const layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

    if(options.centerIcon) {
        const greenIcon = L.icon({
            iconUrl: 'img/leaf-green.png',
            shadowUrl: 'img/leaf-shadow.png',

            iconSize:     [38, 95], // size of the icon
            shadowSize:   [50, 64], // size of the shadow
            iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62],  // the same for the shadow
            popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        L.marker([coordinates.latitude, coordinates.longitude], {icon: greenIcon}).addTo(map);

    }

    map.addLayer(layer);

    if(typeof options.onclick === "function" || options.onclickPopup){
        const popup = L.popup();

        function onMapClick(e) {
            if(options.onclick) {
                options.onclick(e);

            }

            if(options.onclickPopup) {
                popup
                    .setLatLng(e.latlng)
                    .setContent("Az alábbi helyre kattintottál a térképen " + e.latlng.toString())
                    .openOn(map);
            }

        }
        map.on('click', onMapClick);

    }



}