{{-- Peta read-only lokasi sawah (poligon GeoJSON atau pin koordinat) untuk halaman detail. --}}
@if ($project->boundary_geojson || $project->coordinates)
    <div class="field-map-display z-0 h-64 w-full overflow-hidden rounded-2xl border border-stone-200"
         data-coordinates="{{ $project->coordinates }}"
         @if ($project->boundary_geojson) data-boundary='@json($project->boundary_geojson)' @endif></div>
    @vite(['resources/js/field-map.js'])
@endif
