@extends('layouts.public')
@section('title', 'Peta Sekolah')
 
@section('content')
<div class="toolbar">
    <label>Jenjang
        <select id="filter-jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>
    </label>
    <span id="jumlah"></span>
    <button id="unduh" type="button">Unduh GeoJSON</button>
</div>
 
<div id="peta" class="peta"></div>
@endsection
 
@push('scripts')
<script>
    const map = Peta.buat('peta');
    const lapisan = L.layerGroup().addTo(map);
 
    async function muat(jenjang = '') {
        try {
            const fitur = await Peta.ambilSekolah(jenjang);
 
            lapisan.clearLayers();
            fitur.forEach((f) => {
                L.marker(Peta.latlng(f))
                    .bindPopup(Peta.popup(f))
                    .addTo(lapisan);
            });
 
            document.getElementById('jumlah').textContent =
                fitur.length + ' sekolah';
 
            if (fitur.length > 0) {
                map.fitBounds(L.latLngBounds(fitur.map(Peta.latlng)).pad(0.1));
            }
        } catch (e) {
            document.getElementById('jumlah').textContent =
                'Gagal memuat data';
        }
    }
 
    document.getElementById('filter-jenjang')
        .addEventListener('change', (e) => muat(e.target.value));

    // Latihan mandiri: unduh data sekolah yang sedang tampil sebagai GeoJSON
    document.getElementById('unduh').addEventListener('click', async () => {
        const jenjang = document.getElementById('filter-jenjang').value;
        const respon = await fetch(
            `${window.PETA_URL.data}?jenjang=${encodeURIComponent(jenjang)}`
        );
        const data = await respon.json();

        const tautan = document.createElement('a');
        tautan.href = URL.createObjectURL(
            new Blob([JSON.stringify(data, null, 2)], { type: 'application/geo+json' })
        );
        tautan.download = `sekolah${jenjang ? '-' + jenjang : ''}.geojson`;
        tautan.click();
        URL.revokeObjectURL(tautan.href);
    });
  
    muat();
</script>
@endpush
