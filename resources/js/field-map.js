import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '@geoman-io/leaflet-geoman-free';
import '@geoman-io/leaflet-geoman-free/dist/leaflet-geoman.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Ikon marker default Leaflet rusak kalau dibundel Vite — arahkan ke aset bundel.
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

const POLYGON_STYLE = { color: '#059669', weight: 2, fillColor: '#10b981', fillOpacity: 0.25 };
const DEFAULT_CENTER = [-7.7956, 110.3695]; // Yogyakarta, titik awal kalau belum ada lokasi & GPS mati

function addBasemaps(map) {
    const satellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { attribution: 'Tiles &copy; Esri — Source: Esri, Maxar, Earthstar Geographics', maxZoom: 19 },
    );
    const streets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    });

    satellite.addTo(map);
    L.control.layers({ Satelit: satellite, 'Peta Jalan': streets }).addTo(map);
}

function parseCoordinates(text) {
    if (!text) return null;
    const parts = String(text).split(',').map((p) => parseFloat(p.trim()));
    if (parts.length !== 2 || parts.some(Number.isNaN)) return null;
    return L.latLng(parts[0], parts[1]);
}

function formatLatLng(latlng) {
    return `${latlng.lat.toFixed(7)}, ${latlng.lng.toFixed(7)}`;
}

/* ---------------- Picker: modal gambar lokasi sawah ---------------- */

function initPicker() {
    const modal = document.getElementById('field-map-modal');
    if (!modal) return;

    const coordinatesInput = document.getElementById('field-coordinates');
    const boundaryInput = document.getElementById('field-boundary');
    const statusEl = document.getElementById('field-map-status');
    const openers = document.querySelectorAll('[data-open-field-map]');

    let map = null;
    let drawnLayer = null;

    function setStatus(text) {
        if (statusEl) statusEl.textContent = text;
    }

    function describeLayer() {
        if (!drawnLayer) {
            setStatus('Belum ada lokasi. Gambar poligon batas sawah atau taruh pin di peta.');
            return;
        }
        const center = drawnLayer instanceof L.Marker
            ? drawnLayer.getLatLng()
            : drawnLayer.getBounds().getCenter();
        setStatus(`Lokasi terpilih: ${formatLatLng(center)}${drawnLayer instanceof L.Marker ? ' (pin)' : ' (titik tengah poligon)'}`);
    }

    function clearDrawn() {
        if (drawnLayer) {
            map.removeLayer(drawnLayer);
            drawnLayer = null;
        }
    }

    function adoptLayer(layer) {
        clearDrawn();
        drawnLayer = layer;
        if (layer instanceof L.Polygon) layer.setStyle(POLYGON_STYLE);
        layer.on('pm:edit', describeLayer);
        describeLayer();
    }

    function initMap() {
        map = L.map('field-map-canvas');
        addBasemaps(map);
        map.setView(DEFAULT_CENTER, 12);

        map.pm.addControls({
            position: 'topleft',
            drawMarker: true,
            drawPolygon: true,
            drawPolyline: false,
            drawCircle: false,
            drawCircleMarker: false,
            drawRectangle: false,
            drawText: false,
            editMode: true,
            dragMode: false,
            cutPolygon: false,
            removalMode: true,
            rotateMode: false,
        });
        try { map.pm.setLang('id'); } catch (e) { /* bahasa default kalau tidak tersedia */ }

        map.on('pm:create', (e) => adoptLayer(e.layer));
        map.on('pm:remove', (e) => {
            if (e.layer === drawnLayer) {
                drawnLayer = null;
                describeLayer();
            }
        });
        map.on('locationfound', (e) => {
            if (!drawnLayer) {
                adoptLayer(L.marker(e.latlng).addTo(map));
            }
            map.setView(e.latlng, Math.max(map.getZoom(), 16));
        });
    }

    function loadExisting() {
        clearDrawn();

        if (boundaryInput.value) {
            try {
                const geojson = JSON.parse(boundaryInput.value);
                const group = L.geoJSON(geojson, { style: POLYGON_STYLE });
                const layers = group.getLayers();
                if (layers.length) {
                    adoptLayer(layers[0].addTo(map));
                    map.fitBounds(drawnLayer.getBounds(), { padding: [24, 24] });
                    return;
                }
            } catch (e) { /* geojson rusak: abaikan, mulai dari pin */ }
        }

        const existing = parseCoordinates(coordinatesInput.value);
        if (existing) {
            adoptLayer(L.marker(existing).addTo(map));
            map.setView(existing, 16);
            return;
        }

        describeLayer();
        map.locate({ setView: false, timeout: 8000 });
    }

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (!map) initMap();
        // Leaflet harus di-invalidate setelah elemen terlihat, kalau tidak tile-nya rusak.
        setTimeout(() => {
            map.invalidateSize();
            loadExisting();
        }, 60);
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    openers.forEach((el) => el.addEventListener('click', openModal));
    modal.querySelectorAll('[data-close-field-map]').forEach((el) => el.addEventListener('click', closeModal));

    const gpsButton = document.getElementById('field-map-gps');
    if (gpsButton) {
        gpsButton.addEventListener('click', () => map && map.locate({ timeout: 10000 }));
    }

    const confirmButton = document.getElementById('field-map-confirm');
    if (confirmButton) {
        confirmButton.addEventListener('click', () => {
            if (!drawnLayer) {
                coordinatesInput.value = '';
                boundaryInput.value = '';
            } else if (drawnLayer instanceof L.Marker) {
                coordinatesInput.value = formatLatLng(drawnLayer.getLatLng());
                boundaryInput.value = '';
            } else {
                coordinatesInput.value = formatLatLng(drawnLayer.getBounds().getCenter());
                boundaryInput.value = JSON.stringify(drawnLayer.toGeoJSON());
            }
            closeModal();
        });
    }
}

/* ---------------- Peta read-only di halaman detail ---------------- */

function initDisplays() {
    document.querySelectorAll('.field-map-display').forEach((el) => {
        const map = L.map(el, {
            dragging: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false,
            zoomControl: true,
        });
        addBasemaps(map);

        let boundary = null;
        if (el.dataset.boundary) {
            try { boundary = JSON.parse(el.dataset.boundary); } catch (e) { boundary = null; }
        }

        if (boundary) {
            const layer = L.geoJSON(boundary, { style: POLYGON_STYLE }).addTo(map);
            map.fitBounds(layer.getBounds(), { padding: [16, 16] });
            return;
        }

        const point = parseCoordinates(el.dataset.coordinates || '');
        if (point) {
            L.marker(point).addTo(map);
            map.setView(point, 16);
        } else {
            map.setView(DEFAULT_CENTER, 12);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPicker();
    initDisplays();
});
