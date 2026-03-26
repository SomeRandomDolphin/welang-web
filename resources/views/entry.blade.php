@extends('layouts/main')

@section('container')
    @include('sweetalert::alert')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <div class="hNav bg-[#F8FCFF] flex flex-col justify-center items-center py-12">
        <div class=" text-center my-4">
            <h1 class="font-bold text-3xl">Entri Data Laporan Genangan</h1>
            <p class="font-light my-2 text-base text-Inactive">Masukkan data-data laporan genangan yang telah dikumpulkan untuk
                menampilkannya di
                halaman beranda</p>
        </div>

        <form id="searchForm" action="{{ route('entry') }}" method="POST" enctype="multipart/form-data"
            class="w-full sm:w-[90%] xl:w-[60%] border p-5 sm:p-8 rounded-lg bg-white mx-auto">
            @csrf

            @if (session('failed'))
                <div class="w-full bg-red-500 text-white px-4 py-3 rounded-lg text-sm mb-2" role="alert">
                    {{ session('failed') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="w-full bg-red-500 text-white px-4 py-3 rounded-lg text-sm text-left mb-2" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <x-forms.input label="Tanggal Kejadian" classname="w-full my-2" placeholder="Tanggal Kejadian"
                name="tanggal_kejadian" type="datetime-local" value="" />

            <x-forms.input label="Tinggi Genangan" classname="w-full my-2" placeholder="Tinggi Genangan Dalam cm"
                name="tinggi" type="number" value="" />

            <div class="flex flex-col justify-center w-full">
                <label for="foto" class="pFormActive">Foto</label>
                <label for="foto"
                    class="my-2 flex flex-col items-center justify-center w-full min-h-24 border border-gray-200 rounded-lg hover:bg-gray-100">
                    <img src="./camera.svg" alt="icon" class="max-h-128 h-fit rounded-lg" id="file-preview">
                    <p id="file-preview-title"></p>
                    <input id="foto" type="file" class="hidden" name="foto" accept="image/*,.heic,.heif,.webp"
                        onchange="previewImage(event);" />
                </label>
                <div class="flex w-full gap-2">
                    <button type="button" onclick="openCameraPicker()"
                        class="flex-1 py-2 px-3 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 active:bg-blue-700 transition-colors">
                        Ambil dari Kamera
                    </button>
                    <button type="button" onclick="openGalleryPicker()"
                        class="flex-1 py-2 px-3 rounded-lg bg-gray-600 text-white text-sm font-medium hover:bg-gray-700 active:bg-gray-800 transition-colors">
                        Pilih dari Galeri
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-start w-full my-2">
                <label class="block mb-2 pFormActive">Lokasi</label>

                {{-- "Gunakan Lokasi Saya" button at the top --}}
                <button type="button" id="btn-get-location"
                    class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 flex items-center justify-center gap-2 font-medium transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span id="location-btn-text">Gunakan Lokasi Saya</span>
                </button>
                <p id="location-status" class="text-sm mt-1 text-gray-600 min-h-[1.25rem]" role="status" aria-live="polite"></p>

                {{-- Divider --}}
                <div class="flex items-center w-full my-2 gap-x-2">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400 font-medium uppercase">atau cari alamat</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                {{-- Address search input with search button --}}
                <div class="flex w-full gap-x-2">
                    <div class="relative flex-1 z-[1000]">
                        <div class="absolute inset-y-0 flex items-center right-0 pointer-events-none">
                            <svg class="w-4 h-fit mx-3 text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                <path
                                    d="M8 0a7.992 7.992 0 0 0-6.583 12.535 1 1 0 0 0 .12.183l.12.146c.112.145.227.285.326.4l5.245 6.374a1 1 0 0 0 1.545-.003l5.092-6.205c.206-.222.4-.455.578-.7l.127-.155a.934.934 0 0 0 .122-.192A8.001 8.001 0 0 0 8 0Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                            </svg>
                        </div>
                        <input type="text" id="input-address"
                            class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2 pr-8"
                            placeholder="Ketik alamat lalu tekan Cari atau Enter">
                        <div id="address-suggestions"
                            class="hidden absolute top-full left-0 z-[1001] mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-md max-h-60 overflow-auto"
                            role="listbox" aria-label="Saran alamat"></div>
                    </div>
                    <!-- <button type="button" id="btn-search-location"
                        class="flex-shrink-0 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 active:bg-gray-900 text-sm font-medium transition-colors"
                        aria-label="Cari lokasi berdasarkan alamat">
                        Cari
                    </button> -->
                </div>
            </div>

            <div class="flex flex-col justify-center w-full h-[50vh] mt-2 border border-gray-200 rounded-lg" id="map">
            </div>

            <input type="text" class="hidden" id="latitude" name="latitude" required>
            <input type="text" class="hidden" id="longitude" name="longitude" required>
            <x-button message="Unggah" type="submit" color="Primary" link=""
                classname="w-full my-4 py-[10px] text-base" icons="" value="" />
        </form>

    </div>
    <script>
        const MAX_UPLOAD_SIZE_BYTES = 10 * 1024 * 1024;
        const MAX_IMAGE_DIMENSION = 1920;
        const JPEG_QUALITY = 0.82;

        function setLocalDatetimeDefault() {
            const input = document.querySelector('input[name="tanggal_kejadian"]');
            if (!input || input.value) {
                return;
            }

            const now = new Date();
            const localNow = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
                .toISOString()
                .slice(0, 16);

            input.value = localNow;
        }

        function previewImage(event) {
            const input = event.target;
            if (!input.files || !input.files[0]) {
                return;
            }

            updateImagePreview(input.files[0]);
        }

        function updateImagePreview(file) {
            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function() {
                const imgElement = document.getElementById('file-preview');
                const titleElement = document.getElementById('file-preview-title');
                imgElement.classList.add('w-full');
                imgElement.src = reader.result;
                titleElement.textContent = file.name;
            };

            reader.readAsDataURL(file);
        }

        function readFileAsDataURL(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = () => reject(new Error('Gagal membaca file foto.'));
                reader.readAsDataURL(file);
            });
        }

        function loadImage(dataUrl) {
            return new Promise((resolve, reject) => {
                const image = new Image();
                image.onload = () => resolve(image);
                image.onerror = () => reject(new Error('Format foto tidak didukung browser.'));
                image.src = dataUrl;
            });
        }

        function canvasToBlob(canvas, type, quality) {
            return new Promise((resolve) => {
                canvas.toBlob((blob) => resolve(blob), type, quality);
            });
        }

        async function compressImage(file) {
            if (!file || typeof file.type !== 'string' || !file.type.startsWith('image/')) {
                return file;
            }

            const dataUrl = await readFileAsDataURL(file);
            const image = await loadImage(dataUrl);

            const longestSide = Math.max(image.width, image.height);
            const ratio = longestSide > MAX_IMAGE_DIMENSION ? MAX_IMAGE_DIMENSION / longestSide : 1;
            const targetWidth = Math.max(1, Math.round(image.width * ratio));
            const targetHeight = Math.max(1, Math.round(image.height * ratio));

            const canvas = document.createElement('canvas');
            canvas.width = targetWidth;
            canvas.height = targetHeight;

            const context = canvas.getContext('2d');
            if (!context) {
                return file;
            }

            context.drawImage(image, 0, 0, targetWidth, targetHeight);
            const blob = await canvasToBlob(canvas, 'image/jpeg', JPEG_QUALITY);
            if (!blob) {
                return file;
            }

            const baseName = (file.name || 'foto').replace(/\.[^/.]+$/, '');
            return new File([blob], baseName + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });
        }

        function setPhotoStatus(message, isError = false) {
            const titleElement = document.getElementById('file-preview-title');
            if (!titleElement) {
                return;
            }

            titleElement.textContent = message;
            titleElement.className = isError ? 'text-red-600 text-sm mt-1' : 'text-gray-600 text-sm mt-1';
        }

        async function processPhotoBeforeSubmit(event) {
            const form = document.getElementById('searchForm');
            const fileInput = document.getElementById('foto');

            if (!form || !fileInput || !fileInput.files || !fileInput.files[0]) {
                return;
            }

            event.preventDefault();

            const originalFile = fileInput.files[0];
            let finalFile = originalFile;

            try {
                setPhotoStatus('Sedang mengompresi foto dari kamera...');
                finalFile = await compressImage(originalFile);
            } catch (error) {
                console.warn('Kompresi foto dilewati:', error);
                finalFile = originalFile;
            }

            if (finalFile.size > MAX_UPLOAD_SIZE_BYTES) {
                setPhotoStatus('Foto terlalu besar. Coba ambil ulang dengan resolusi lebih rendah.', true);
                return;
            }

            if (typeof DataTransfer !== 'undefined') {
                const transfer = new DataTransfer();
                transfer.items.add(finalFile);
                fileInput.files = transfer.files;
            }

            updateImagePreview(finalFile);

            if (finalFile.size < originalFile.size) {
                setPhotoStatus('Foto berhasil dikompresi dan siap diunggah.');
            }

            form.submit();
        }

        function openCameraPicker() {
            const fileInput = document.getElementById('foto');
            fileInput.setAttribute('capture', 'environment');
            fileInput.click();
        }

        function openGalleryPicker() {
            const fileInput = document.getElementById('foto');
            fileInput.removeAttribute('capture');
            fileInput.click();
        }

        (function setupImageCompressionBeforeSubmit() {
            const form = document.getElementById('searchForm');
            if (!form) {
                return;
            }

            form.addEventListener('submit', processPhotoBeforeSubmit);
        })();

        setLocalDatetimeDefault();
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let marker;
        let defaultLat = -7.2575;
        let defaultLng = 112.7521;

        function initMap() {
            map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Check if site is using HTTPS (required for mobile geolocation)
            const statusEl = document.getElementById('location-status');
            
            // Add button click handler for manual location request
            document.getElementById('btn-get-location').addEventListener('click', function() {
                getUserLocation();
            });

            function getUserLocation() {
                const btnText = document.getElementById('location-btn-text');
                const statusEl = document.getElementById('location-status');
                
                if (!navigator.geolocation) {
                    handleLocationError(false);
                    return;
                }

                // Show loading state
                btnText.textContent = 'Mendapatkan lokasi...';
                statusEl.textContent = 'Mohon izinkan akses lokasi saat diminta';
                statusEl.className = 'text-sm mt-1 text-blue-600';

                // Geolocation options optimized for mobile
                const options = {
                    enableHighAccuracy: true, // Use GPS on mobile
                    timeout: 10000, // 10 second timeout
                    maximumAge: 0 // Don't use cached position
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.setView([lat, lng], 17);

                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng]).addTo(map)
                                .bindPopup('Lokasi Anda');
                        }

                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;

                        // Success feedback
                        btnText.textContent = 'Lokasi Berhasil Diperoleh ✓';
                        statusEl.textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        statusEl.className = 'text-sm mt-1 text-green-600';
                        
                        setTimeout(() => {
                            btnText.textContent = 'Perbarui Lokasi Saya';
                        }, 2000);
                    },
                    (error) => {
                        handleLocationError(true, error);
                    },
                    options
                );
            }

            function handleLocationError(browserHasGeolocation, error) {
                const btnText = document.getElementById('location-btn-text');
                const statusEl = document.getElementById('location-status');
                
                btnText.textContent = 'Coba Lagi';
                
                let errorMessage = '';
                if (!browserHasGeolocation) {
                    errorMessage = 'Browser Anda tidak mendukung geolokasi.';
                } else if (error) {
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Akses lokasi ditolak. Silakan izinkan di pengaturan browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Waktu permintaan lokasi habis. Coba lagi.';
                            break;
                        default:
                            errorMessage = 'Terjadi kesalahan saat mendapatkan lokasi.';
                    }
                } else {
                    errorMessage = 'Gagal mendapatkan lokasi.';
                }
                
                statusEl.textContent = '❌ ' + errorMessage;
                statusEl.className = 'text-sm mt-1 text-red-600';
                console.error(errorMessage, error);
            }

            // Handle address search
            const addressInput = document.getElementById('input-address');
            const suggestionsBox = document.getElementById('address-suggestions');
            let suggestionDebounceTimer = null;
            let suggestionController = null;

            function hideSuggestions() {
                suggestionsBox.classList.add('hidden');
                suggestionsBox.innerHTML = '';
            }

            function showSuggestionLoading() {
                suggestionsBox.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Mencari saran alamat...</div>';
                suggestionsBox.classList.remove('hidden');
            }

            function renderSuggestions(places) {
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
                    .then((data) => {
                        renderSuggestions(data);
                    })
                    .catch((error) => {
                        if (error.name !== 'AbortError') {
                            console.error('Suggestion error:', error);
                            hideSuggestions();
                        }
                    });
            }

            addressInput.addEventListener('input', function() {
                const query = typeof addressInput.value === 'string' ? addressInput.value.trim() : '';

                if (suggestionDebounceTimer) {
                    clearTimeout(suggestionDebounceTimer);
                }

                if (query.length < 2 || !isValidAddress(query)) {
                    hideSuggestions();
                    return;
                }

                showSuggestionLoading();
                suggestionDebounceTimer = setTimeout(() => {
                    fetchSuggestions(query);
                }, 200);
            });

            addressInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    hideSuggestions();
                    searchLocation();
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

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup('Selected Location');
                }

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            });

            document.addEventListener('click', function(event) {
                if (!event.target.closest('#input-address') && !event.target.closest('#address-suggestions')) {
                    hideSuggestions();
                }
            });

            const searchButton = document.getElementById('btn-search-location');
            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    searchLocation();
                });
            }

            function isValidAddress(address) {
                // Basic validation: non-empty, reasonable length, and no clearly dangerous characters
                if (typeof address !== 'string') {
                    return false;
                }

                const trimmed = address.trim();
                if (trimmed.length === 0 || trimmed.length > 200) {
                    return false;
                }

                // Disallow characters that are unlikely in a normal address and may be used for injection
                const invalidPattern = /[<>\\{}]/;
                if (invalidPattern.test(trimmed)) {
                    return false;
                }

                return true;
            }

            function searchLocation() {
                const rawAddress = addressInput.value;
                const address = typeof rawAddress === 'string' ? rawAddress.trim() : '';

                if (!address) {
                    return;
                }

                if (!isValidAddress(address)) {
                    console.warn('Invalid address input rejected.');
                    return;
                }

                const url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address);

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Geocoding request failed with status ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            alert('Alamat tidak ditemukan. Silakan periksa kembali alamat yang dimasukkan.');
                            return;
                        }

                        const place = data[0];
                        const lat = parseFloat(place.lat);
                        const lng = parseFloat(place.lon);

                        map.setView([lat, lng], 17);

                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng]).addTo(map)
                                .bindPopup('Selected Location');
                        }

                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mencari alamat. Periksa koneksi internet Anda dan coba lagi.');
                    });
            }

            // Handle map clicks
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup('Selected Location');
                }

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            });
        }

        // Initialize map on page load
        (function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMap);
            } else {
                initMap();
            }
        })();
    </script>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
