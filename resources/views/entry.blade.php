@extends('layouts/main')

@section('container')
    @include('sweetalert::alert')
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
                <div class="relative w-full">
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
    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3j--iDhYGf5VEqBQBTSF46W1nBDKqgfk&libraries=places&callback=initMap&loading=async">
    </script>

    <script>
        let map;
        let marker;
        let autocomplete;

        function initMap() {

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: true,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [{
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "transit.station.bus",
                        stylers: [{
                            visibility: "off"
                        }]
                    },
                    {
                        featureType: "transit.station.rail",
                        stylers: [{
                            visibility: "off"
                        }]
                    }
                ]
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        map.setCenter(pos);

                        marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: "Your Location",
                        });

                        document.getElementById("latitude").value = pos.lat;
                        document.getElementById("longitude").value = pos.lng;
                        document.getElementById("coordinates").innerText =
                            `Coordinates:\nLatitude: ${pos.lat}\nLongitude: ${pos.lng}`;
                    },
                    () => {
                        handleLocationError(true, map.getCenter());
                    }
                );
            } else {
                handleLocationError(false, map.getCenter());
            }

            function handleLocationError(browserHasGeolocation, pos) {
                const errorMessage = browserHasGeolocation ?
                    'Error: The Geolocation service failed.' :
                    'Error: Your browser doesn\'t support geolocation.';
                console.error(errorMessage);

                document.getElementById("coordinates").innerText =
                    `Default Coordinates:\nLatitude: ${pos.lat()}\nLongitude: ${pos.lng()}`;
            }

            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("input-address"), {
                    types: ["geocode"]
                }
            );

            autocomplete.bindTo("bounds", map);
            autocomplete.addListener("place_changed", onPlaceChanged);

            function onPlaceChanged() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                if (marker) {
                    marker.setPosition(place.geometry.location);
                } else {
                    marker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                        title: "Selected Location",
                    });
                }

                document.getElementById("latitude").value = place.geometry.location.lat();
                document.getElementById("longitude").value = place.geometry.location.lng();

                document.getElementById("coordinates").innerText =
                    `Coordinates:\nLatitude: ${place.geometry.location.lat()}\nLongitude: ${place.geometry.location.lng()}`;
            }

            map.addListener("click", (mapsMouseEvent) => {
                const clickedLatLng = {
                    lat: mapsMouseEvent.latLng.lat(),
                    lng: mapsMouseEvent.latLng.lng(),
                };

                marker.setPosition(clickedLatLng);
                document.getElementById("latitude").value = clickedLatLng.lat;
                document.getElementById("longitude").value = clickedLatLng.lng;
                document.getElementById("coordinates").innerText =
                    `coordinates:\nLatitude: ${clickedLatLng.lat}\nLongitude: ${clickedLatLng.lng}`;
            });
        }
    </script>
    <script>
        let map;
        let marker;
        let autocomplete;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        map.setCenter(pos);

                        marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: "Your Location",
                        });

                        document.getElementById("latitude").value = pos.lat;
                        document.getElementById("longitude").value = pos.lng;
                        document.getElementById("coordinates").innerText =
                            `Coordinates:\nLatitude: ${pos.lat}\nLongitude: ${pos.lng}`;
                    },
                    () => {
                        handleLocationError(true, map.getCenter());
                    }
                );
            } else {
                handleLocationError(false, map.getCenter());
            }

            function handleLocationError(browserHasGeolocation, pos) {
                const errorMessage = browserHasGeolocation ?
                    'Error: The Geolocation service failed.' :
                    'Error: Your browser doesn\'t support geolocation.';
                console.error(errorMessage);

                document.getElementById("coordinates").innerText =
                    `Default Coordinates:\nLatitude: ${pos.lat()}\nLongitude: ${pos.lng()}`;
            }

            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("input-address"), {
                    types: ["geocode"]
                }
            );

            autocomplete.bindTo("bounds", map);
            autocomplete.addListener("place_changed", onPlaceChanged);

            function onPlaceChanged() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                if (marker) {
                    marker.setPosition(place.geometry.location);
                } else {
                    marker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                        title: "Selected Location",
                    });
                }

                document.getElementById("latitude").value = place.geometry.location.lat();
                document.getElementById("longitude").value = place.geometry.location.lng();

                document.getElementById("coordinates").innerText =
                    `Coordinates:\nLatitude: ${place.geometry.location.lat()}\nLongitude: ${place.geometry.location.lng()}`;
            }
        }
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
