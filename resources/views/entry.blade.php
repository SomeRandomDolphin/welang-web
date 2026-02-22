@extends('layouts/main')

@section('container')
    @include('sweetalert::alert')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <div class="hNav bg-[#F8FCFF] flex flex-col justify-center items-center py-12">
        <div class=" text-center my-4">
            <h1 class="font-bold text-3xl">Entri Data Survei</h1>
            <p class="font-light my-2 text-base text-Inactive"">Masukkan data-data survei yang telah dikumpulkan untuk
                menampilkannya di
                halaman beranda</p>
        </div>

        <form id="searchForm" action="{{ route('entry') }}" method="POST" enctype="multipart/form-data"
            class="w-[90%] xl:w-[60%] border p-8 rounded-lg bg-white mx-auto">
            @csrf
            <x-forms.input label="Tanggal Kejadian" classname="w-full my-2" placeholder="Tanggal Kejadian"
                name="tanggal_kejadian" type="datetime-local" value="" />

            <x-forms.input label="Tinggi Genangan" classname="w-full my-2" placeholder="Tinggi Genangan Dalam cm"
                name="tinggi" type="number" value="" />

            <div class="flex flex-col justify-center w-full">
                <label for="foto" class="pFormActive">Foto</label>
                <label for="foto"
                    class="my-2 flex flex-col items-center justify-center w-full min-h-24 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100">
                    <img src="./camera.svg" alt="icon" class="max-h-128 h-fit rounded-lg" id="file-preview">
                    <p id="file-preview-title"></p>
                    <input id="foto" type="file" class="hidden" name="foto" accept="image/*"
                        onchange="previewImage(event);" />
                </label>
            </div>

            <div class="flex flex-col items-start w-full my-2">
                <label for="lokasi" class="block mb-2 pFormActive">Lokasi</label>
                <div class="relative w-full mb-2">
                    <div class="absolute inset-y-0 flex items-center right-0 pointer-events-none">
                        <svg class="w-4 h-fit mx-5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                            <path
                                d="M8 0a7.992 7.992 0 0 0-6.583 12.535 1 1 0 0 0 .12.183l.12.146c.112.145.227.285.326.4l5.245 6.374a1 1 0 0 0 1.545-.003l5.092-6.205c.206-.222.4-.455.578-.7l.127-.155a.934.934 0 0 0 .122-.192A8.001 8.001 0 0 0 8 0Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                        </svg>
                    </div>
                    <input type="text" id="input-address"
                        class="border border-gray-200 pFormActive font-light rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full"
                        placeholder="Masukkan Lokasi Kejadian">
                </div>
                <button type="button" id="btn-get-location" 
                    class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span id="location-btn-text">Gunakan Lokasi Saya</span>
                </button>
                <p id="location-status" class="text-sm mt-1 text-gray-600"></p>
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
        document.getElementById('customDatePicker').addEventListener('click', function() {
            document.getElementById('hiddenDatePicker').click();
        });
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/nominatim-js@3.1.0/build/nominatim.min.js"></script>

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
            const isSecure = window.location.protocol === 'https:';
            const statusEl = document.getElementById('location-status');
            
            if (!isSecure) {
                statusEl.textContent = '⚠️ Peringatan: Geolokasi di perangkat mobile memerlukan HTTPS';
                statusEl.className = 'text-sm mt-1 text-orange-600';
            }

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
            addressInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchLocation();
                }
            });

            function searchLocation() {
                const address = addressInput.value;
                if (!address) return;

                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
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
    <script>
        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const imgElement = document.getElementById('file-preview');
                imgElement.classList.add('w-full');
                imgElement.src = reader.result;
                titleElement.textContent = input.files[0].name;
            };

            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
