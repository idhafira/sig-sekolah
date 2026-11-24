@php
    $teks = [
        'npsn' => 'NPSN (8 digit)',
        'nama' => 'Nama Sekolah',
        'kecamatan' => 'Kecamatan',
        'telepon' => 'Telepon',
    ];
    $pilihan = [
        'jenjang' => ['SD', 'SMP', 'SMA', 'SMK'],
        'status' => ['Negeri', 'Swasta'],
    ];
    $cls = 'w-full border rounded px-3 py-2';
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .sekolah-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        background: #fff;
        padding: 1.5rem;
        border-radius: 0.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }
    @media (min-width: 768px) {
        .sekolah-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    .sekolah-grid > div {
        min-width: 0;
    }
    .sekolah-grid .field {
        margin-bottom: 1rem;
    }
    .sekolah-grid .field label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .sekolah-grid .field input,
    .sekolah-grid .field select,
    .sekolah-grid .field textarea {
        width: 100%;
        box-sizing: border-box;
    }
    .sekolah-grid .koordinat {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }
    .sekolah-grid .aksi {
        display: flex;
        gap: 0.75rem;
        padding-top: 0.5rem;
    }
    #map-picker {
        height: 420px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 0.25rem;
        margin-top: 0.5rem;
    }
</style>

<div class="sekolah-grid">
    {{-- KIRI: form --}}
    <div>
        @foreach ($teks as $name => $label)
            <div class="field">
                <label for="{{ $name }}">{{ $label }}</label>
                <input type="text" id="{{ $name }}" name="{{ $name }}"
                    value="{{ old($name, $sekolah->$name) }}"
                    @readonly($name === 'npsn' && auth()->user()->isSekolah())
                    class="{{ $cls }}">
                @error($name)
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        @foreach ($pilihan as $name => $opsi)
            <div class="field">
                <label for="{{ $name }}">{{ ucfirst($name) }}</label>
                <select id="{{ $name }}" name="{{ $name }}" class="{{ $cls }}">
                    <option value="">-- Pilih --</option>
                    @foreach ($opsi as $o)
                        <option value="{{ $o }}" @selected(old($name, $sekolah->$name) === $o)>
                            {{ $o }}
                        </option>
                    @endforeach
                </select>
                @error($name)
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="field">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" class="{{ $cls }}">{{ old('alamat', $sekolah->alamat) }}</textarea>
            @error('alamat')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="aksi">
            <button type="submit" class="px-5 py-2 bg-blue-700 text-white rounded">Simpan</button>
            <a href="{{ auth()->user()->isDinas()
                ? route('dinas.sekolah.index')
                : route('dashboard') }}"
                class="px-5 py-2 bg-gray-300 rounded">Batal</a>
        </div>
    </div>

    {{-- KANAN: peta + koordinat --}}
    <div>
        <p class="font-medium">Klik peta atau geser marker untuk menentukan lokasi sekolah</p>

        <div id="map-picker"></div>

        <div class="koordinat field">
            <div>
                <label for="latitude">Latitude</label>
                <input type="number" step="any" id="latitude" name="latitude"
                    value="{{ old('latitude', $sekolah->latitude) }}" class="{{ $cls }}">
                @error('latitude')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="longitude">Longitude</label>
                <input type="number" step="any" id="longitude" name="longitude"
                    value="{{ old('longitude', $sekolah->longitude) }}" class="{{ $cls }}">
                @error('longitude')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        const sudahAda = latInput.value !== '' && lngInput.value !== '';
        const pusat = sudahAda
            ? [parseFloat(latInput.value), parseFloat(lngInput.value)]
            : [-3.3194, 114.5908]; // pusat Kota Banjarmasin

        const map = L.map('map-picker').setView(pusat, sudahAda ? 16 : 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const marker = L.marker(pusat, { draggable: true }).addTo(map);

        function isiKoordinat(lat, lng) {
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
        }

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            isiKoordinat(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function () {
            const p = marker.getLatLng();
            isiKoordinat(p.lat, p.lng);
        });

        [latInput, lngInput].forEach(function (el) {
            el.addEventListener('change', function () {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 16);
                }
            });
        });

        setTimeout(function () { map.invalidateSize(); }, 200);
    })();
</script>