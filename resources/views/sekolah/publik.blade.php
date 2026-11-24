@extends('layouts.app')
@section('title', $sekolah->nama)
@section('content')
@php $p = $sekolah->profil; @endphp

<a href="{{ url('/') }}" class="text-sm text-blue-700">&larr; Kembali ke peta</a>

<h1 class="text-2xl font-bold mt-2">{{ $sekolah->nama }}</h1>
<p class="text-gray-600 mb-4">
    NPSN {{ $sekolah->npsn }} - {{ $sekolah->jenjang }} {{ $sekolah->status }}
</p>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-bold text-lg mb-3">Informasi Sekolah</h2>
        <dl class="grid grid-cols-3 gap-y-2 text-sm">
            <dt class="text-gray-500">Alamat</dt>
            <dd class="col-span-2">{{ $sekolah->alamat }}, {{ $sekolah->kecamatan }}</dd>

            <dt class="text-gray-500">Telepon</dt>
            <dd class="col-span-2">{{ $sekolah->telepon ?: '-' }}</dd>

            <dt class="text-gray-500">Kepala sekolah</dt>
            <dd class="col-span-2">{{ $p?->kepala_sekolah ?: '-' }}</dd>

            <dt class="text-gray-500">Tahun berdiri</dt>
            <dd class="col-span-2">{{ $p?->tahun_berdiri ?: '-' }}</dd>

            <dt class="text-gray-500">Akreditasi</dt>
            <dd class="col-span-2">{{ $p?->akreditasi ?: '-' }}</dd>

            <dt class="text-gray-500">Jumlah siswa</dt>
            <dd class="col-span-2">{{ $p?->jumlah_siswa ?? '-' }}</dd>

            <dt class="text-gray-500">Jumlah guru</dt>
            <dd class="col-span-2">{{ $p?->jumlah_guru ?? '-' }}</dd>
        </dl>

        <h2 class="font-bold text-lg mt-6 mb-2">Deskripsi</h2>
        @if ($p?->deskripsi)
            <p class="text-sm">{!! nl2br(e($p->deskripsi)) !!}</p>
        @else
            <p class="text-sm text-gray-500">Sekolah belum melengkapi deskripsi.</p>
        @endif
    </div>

    <div class="bg-white rounded shadow p-2">
        <div id="mini-peta" style="height: 360px;" class="rounded"></div>
        <p class="text-xs text-gray-500 p-2">
            Koordinat: {{ $sekolah->latitude }}, {{ $sekolah->longitude }}
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const lat = {{ (float) $sekolah->latitude }};
    const lng = {{ (float) $sekolah->longitude }};
    const map = L.map('mini-peta').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    L.marker([lat, lng]).addTo(map);
</script>
@endpush