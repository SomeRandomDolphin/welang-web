@extends('layouts/main')

@section('container')
    @include('sweetalert::alert')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        .scrollbar-visible {
            scrollbar-width: auto;
            scrollbar-color: #94a3b8 #e5e7eb;
            scrollbar-gutter: stable;
        }

        .scrollbar-visible::-webkit-scrollbar {
            width: 10px;
        }

        .scrollbar-visible::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 9999px;
        }

        .scrollbar-visible::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 9999px;
            border: 2px solid #e5e7eb;
        }
    </style>

    <div class="hNav bg-[#F8FCFF] flex flex-col justify-center items-center py-12 px-4">
        <div class="w-full sm:w-[90%] xl:w-[60%] text-center my-4">
            <h1 class="font-bold text-3xl">Detail Laporan Genangan</h1>
            <p class="font-light my-2 text-base text-Inactive">Kelola data laporan: lihat detail, edit, atau hapus dengan tampilan seperti halaman entri.</p>
        </div>

        <div class="w-full sm:w-[90%] xl:w-[60%] flex justify-end mb-3">
            <a href="{{ route('history') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kembali ke Riwayat
            </a>
        </div>

        <div class="w-full sm:w-[90%] xl:w-[60%] border p-5 sm:p-8 rounded-lg bg-white mx-auto">
            @if ($errors->any())
                <div class="w-full bg-red-500 text-white px-4 py-3 rounded-lg text-sm text-left mb-4" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @php
                $formattedDate = \Carbon\Carbon::parse($survey->tanggal_kejadian)->format('Y-m-d\\TH:i');
            @endphp

            <div class="w-full my-2 rounded-lg border border-gray-200 bg-slate-50 p-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500">Petugas</p>
                        <p class="font-medium text-gray-900">{{ $survey->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">ID Laporan</p>
                        <p class="font-medium text-gray-900">#{{ $survey->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Diperbarui Oleh</p>
                        <p class="font-medium text-gray-900">{{ $survey->updatedBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Waktu Update</p>
                        <p class="font-medium text-gray-900">{{ $survey->updatedBy ? \Carbon\Carbon::parse($survey->updated_at)->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>

            <form id="adminHistoryForm" action="{{ route('admin.history.update', $survey->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_kejadian" class="block mb-2 pFormActive">Tanggal Kejadian</label>
                        <input type="datetime-local" id="tanggal_kejadian" name="tanggal_kejadian"
                            value="{{ old('tanggal_kejadian', $formattedDate) }}"
                            class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div>
                        <label for="tinggi" class="block mb-2 pFormActive">Tinggi Genangan (cm)</label>
                        <input type="number" step="0.01" min="0" id="tinggi" name="tinggi"
                            value="{{ old('tinggi', $survey->tinggi) }}"
                            class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>

                <div class="flex flex-col justify-center w-full">
                    <label for="foto" class="pFormActive">Foto Baru (opsional)</label>
                    <label for="foto"
                        class="my-2 flex flex-col items-center justify-center w-full min-h-24 border border-gray-200 rounded-lg hover:bg-gray-100">
                        @if ($survey->foto && $survey->foto !== '/')
                            <img src="{{ asset('storage/' . $survey->foto) }}" alt="foto laporan"
                                class="w-full max-h-64 object-cover rounded-lg" id="file-preview">
                        @else
                            <img src="{{ secure_asset('./camera.svg') }}" alt="icon"
                                class="max-h-40 h-fit rounded-lg" id="file-preview">
                        @endif
                        <p id="file-preview-title" class="text-gray-600 text-sm mt-1"></p>
                        <input id="foto" type="file" class="hidden" name="foto" accept="image/*,.heic,.heif,.webp"
                            onchange="previewImage(event);" />
                    </label>
                </div>

                <div class="flex flex-col items-start w-full my-2">
                    <label class="block mb-2 pFormActive">Lokasi</label>

                    <button type="button" id="btn-get-location"
                        class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 flex items-center justify-center gap-2 font-medium transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span id="location-btn-text">Gunakan Lokasi Saya</span>
                    </button>
                    <p id="location-status" class="text-sm mt-1 text-gray-600 min-h-[1.25rem]" role="status" aria-live="polite"></p>

                    <div class="flex items-center w-full my-2 gap-x-2">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs text-gray-400 font-medium uppercase">atau cari alamat</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <div class="flex w-full gap-x-2">
                        <div class="relative flex-1 z-[1000]">
                            <div class="absolute inset-y-0 flex items-center right-0 pointer-events-none">
                                <svg class="w-4 h-fit mx-3 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 16 20">
                                    <path
                                        d="M8 0a7.992 7.992 0 0 0-6.583 12.535 1 1 0 0 0 .12.183l.12.146c.112.145.227.285.326.4l5.245 6.374a1 1 0 0 0 1.545-.003l5.092-6.205c.206-.222.4-.455.578-.7l.127-.155a.934.934 0 0 0 .122-.192A8.001 8.001 0 0 0 8 0Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                                </svg>
                            </div>
                            <input type="text" id="input-address"
                                class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2 pr-8"
                                placeholder="Ketik alamat lalu tekan Enter">
                            <div id="address-suggestions"
                                class="hidden absolute top-full left-0 z-[1001] mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-md max-h-60 overflow-auto"
                                role="listbox" aria-label="Saran alamat"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block mb-2 pFormActive">Latitude</label>
                        <input type="number" step="0.00000001" id="latitude" name="latitude"
                            value="{{ old('latitude', $survey->latitude) }}" readonly
                            class="border border-gray-200 pFormActive font-light rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div>
                        <label for="longitude" class="block mb-2 pFormActive">Longitude</label>
                        <input type="number" step="0.00000001" id="longitude" name="longitude"
                            value="{{ old('longitude', $survey->longitude) }}" readonly
                            class="border border-gray-200 pFormActive font-light rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>

                <div class="flex flex-col justify-center w-full h-[50vh] mt-2 border border-gray-200 rounded-lg" id="map"></div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <form action="{{ route('admin.history.delete', $survey->id) }}" method="POST" class="mt-3"
                onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Hapus Data
                </button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        let suggestionDebounceTimer = null;
        let suggestionController = null;

        function previewImage(event) {
            const input = event.target;
            if (!input.files || !input.files[0]) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function() {
                const imgElement = document.getElementById('file-preview');
                const titleElement = document.getElementById('file-preview-title');
                imgElement.classList.add('w-full');
                imgElement.classList.add('max-h-64');
                imgElement.classList.add('object-cover');
                imgElement.src = reader.result;
                titleElement.textContent = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }

        function isValidAddress(address) {
            if (typeof address !== 'string') {
                return false;
            }

            const trimmed = address.trim();
            if (trimmed.length === 0 || trimmed.length > 200) {
                return false;
            }

            return !/[<>\\{}]/.test(trimmed);
        }

        function setCoordinates(lat, lng) {
            document.getElementById('latitude').value = Number(lat).toFixed(8);
            document.getElementById('longitude').value = Number(lng).toFixed(8);
        }

        function placeMarker(lat, lng, popupText = 'Lokasi dipilih') {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map).bindPopup(popupText);
            }

            setCoordinates(lat, lng);
        }

        function hideSuggestions() {
            const suggestionsBox = document.getElementById('address-suggestions');
            suggestionsBox.classList.add('hidden');
            suggestionsBox.innerHTML = '';
        }

        function renderSuggestions(places) {
            const suggestionsBox = document.getElementById('address-suggestions');
            if (!Array.isArray(places) || places.length === 0) {
                hideSuggestions();
                return;
            }

            const html = places.map((place) => {
                const name = String(place.display_name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                const lat = String(place.lat || '');
                const lon = String(place.lon || '');
                return `<button type="button" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 border-b border-gray-100 last:border-b-0" data-lat="${lat}" data-lon="${lon}" data-name="${name}" role="option">${name}</button>`;
            }).join('');

            suggestionsBox.innerHTML = html;
            suggestionsBox.classList.remove('hidden');
        }

        function fetchSuggestions(query) {
            if (suggestionController) {
                suggestionController.abort();
            }

            suggestionController = new AbortController();
            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&addressdetails=1&q=' + encodeURIComponent(query);

            fetch(url, {
                    signal: suggestionController.signal
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Suggestion request failed with status ' + response.status);
                    }
                    return response.json();
                })
                .then((data) => renderSuggestions(data))
                .catch((error) => {
                    if (error.name !== 'AbortError') {
                        hideSuggestions();
                    }
                });
        }

        function initMap() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const initialLat = parseFloat(latInput.value) || -7.2575;
            const initialLng = parseFloat(lngInput.value) || 112.7521;

            map = L.map('map').setView([initialLat, initialLng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            placeMarker(initialLat, initialLng, 'Lokasi laporan');

            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                placeMarker(lat, lng);
            });

            const addressInput = document.getElementById('input-address');
            const suggestionsBox = document.getElementById('address-suggestions');

            addressInput.addEventListener('input', function() {
                const query = typeof addressInput.value === 'string' ? addressInput.value.trim() : '';

                if (suggestionDebounceTimer) {
                    clearTimeout(suggestionDebounceTimer);
                }

                if (query.length < 2 || !isValidAddress(query)) {
                    hideSuggestions();
                    return;
                }

                suggestionDebounceTimer = setTimeout(() => fetchSuggestions(query), 200);
            });

            addressInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    hideSuggestions();
                }
            });

            suggestionsBox.addEventListener('click', function(event) {
                const target = event.target.closest('button[data-lat][data-lon]');
                if (!target) {
                    return;
                }

                const lat = parseFloat(target.dataset.lat);
                const lng = parseFloat(target.dataset.lon);
                const name = target.dataset.name || '';

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    return;
                }

                addressInput.value = name;
                hideSuggestions();
                map.setView([lat, lng], 17);
                placeMarker(lat, lng);
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('#input-address') && !event.target.closest('#address-suggestions')) {
                    hideSuggestions();
                }
            });

            document.getElementById('btn-get-location').addEventListener('click', function() {
                const btnText = document.getElementById('location-btn-text');
                const statusEl = document.getElementById('location-status');

                if (!navigator.geolocation) {
                    statusEl.textContent = 'Browser Anda tidak mendukung geolokasi.';
                    statusEl.className = 'text-sm mt-1 text-red-600';
                    return;
                }

                btnText.textContent = 'Mendapatkan lokasi...';
                statusEl.textContent = 'Mohon izinkan akses lokasi saat diminta';
                statusEl.className = 'text-sm mt-1 text-blue-600';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.setView([lat, lng], 17);
                        placeMarker(lat, lng, 'Lokasi Anda');

                        btnText.textContent = 'Lokasi Berhasil Diperoleh';
                        statusEl.textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        statusEl.className = 'text-sm mt-1 text-green-600';

                        setTimeout(() => {
                            btnText.textContent = 'Perbarui Lokasi Saya';
                        }, 1500);
                    },
                    () => {
                        btnText.textContent = 'Coba Lagi';
                        statusEl.textContent = 'Gagal mendapatkan lokasi. Periksa izin lokasi browser.';
                        statusEl.className = 'text-sm mt-1 text-red-600';
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMap);
            } else {
                initMap();
            }
        })();
    </script>
@endsection
