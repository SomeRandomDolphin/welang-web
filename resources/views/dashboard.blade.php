<!DOCTYPE html>
<html lang="en">

<head>
    {{-- @vite('resources/css/app.css', 'resources/js/app.js') --}}
    <link href="{{ secure_asset('build/assets/app-GJGlotqp.css ') }}" rel="stylesheet">


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ secure_asset('/favicon.ico') }}" type="image/x-icon">
    <title>ITS | Informasi Banjir</title>
</head>

<body>
    @include('sweetalert::alert')
    @include('partials/navbar')
    <div class="bg-[#F8FCFF] w-screen">
        <div class="h-nav p-5 flex gap-x-5">
            <div
                class="hidden md:flex md:flex-col md:justify-between border h-full w-[25%] rounded-lg p-5 bg-white drop-shadow-sm">

                @if (auth()->check() && auth()->user()->is_admin)
                    <div class="fRow justify-between">
                        <h2 class="textBlack font-medium">Keterangan Kategori</h2>
                        <button data-modal-target="iconUpdate-modal" data-modal-toggle="iconUpdate-modal" class=""
                            type="button">
                            <img src={{ secure_asset('/Edit.svg') }} alt="">
                        </button>
                    </div>
                @else
                    <h2 class="textBlack font-medium">Keterangan Kategori</h2>
                @endif



                @foreach ($categories as $item)
                    <div
                        class="fRow w-full h-[17.5%] p-2 gap-x-3 rounded-md hover:bg-slate-100 justify-center items-center">
                        <img src="{{ $item['ikon'] }}" alt="" class="h-[65%]">
                        <div>
                            <h3 class="textPrimary font-medium">Kategori {{ $item['jenis'] }}</h3>
                            <p class="textSecondary">
                                Menunjukkan bahwa genangan air memiliki kedalaman antara {{ $item['tinggi_minimal'] }}
                                cm hingga {{ $item['tinggi_maksimal'] }} cm.
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>

            <x-modal :categories="$categories" />

            <div class="h-full w-full md:w-[75%] flex flex-col gap-y-2">
                <form id="filterForm" action="{{ route('dashboard') }}" method="GET"
                    class="border h-[40%] md:h-[12%] rounded-lg bg-white flex justify-evenly md:justify-between items-center md:px-10 drop-shadow-sm">

                    <h2 class="text-sm text-graySecondary hidden md:block">Filter dan Lokasi</h2>

                    <div class="flex flex-col md:flex-row text-grayPrimary items-center h-full md:gap-x-4 py-2">
                        <div class="flex flex-col md:flex-row h-full items-center gap-y-1">
                        <h3 class="textFilter font-medium">TANGGAL</h3>
                        <div class="flex justify-center items-center">
                            <input datepicker datepicker-format="yyyy-mm-dd" type="date" name="start"
                                id="startDateInput"
                                class="border mx-1 rounded-lg border-slate-500/20 text-center focus:ring-0 focus: pFormActive font-light"
                                placeholder="Select date" value="{{ $filter['start_date'] ?? '' }}">

                            <p>-</p>

                            <input datepicker datepicker-format="yyyy-mm-dd" type="date" name="end"
                                id="endDateInput"
                                class="border mx-1 rounded-lg border-slate-500/20 text-center focus:ring-0 focus: pFormActive font-light"
                                placeholder="Select date end" value="{{ $filter['end_date'] ?? '' }}">
                            
                        </div>
                        </div>
                        

                        <div class="flex flex-col justify-start items-center md:flex-row w-full h-full md:gap-x-2">
                        <h3 class="textFilter font-medium">TINGGI</h3>
                        <div class="flex justify-center items-center gap-x-3 w-full h-full">
                            <input name="min" type="number" id="heightInput"
                                class="border rounded-lg border-slate-500/20 text-black text-center focus:ring-0 focus:border-none pFormActive font-light"
                                placeholder="0" min="0" max="100" value="{{ $filter['min_height'] ?? '' }}">

                            <p>-</p>

                            <input name="max" type="number" id="heightInput"
                                class="border rounded-lg border-slate-500/20 text-black text-center focus:ring-0 focus:border-none pFormActive font-light"
                                placeholder="0" min="0" max="100" value="{{ $filter['max_height'] ?? '' }}">
                            <p class="text-gray-500 font-light">Centimeter</p>


                        <x-button message="Filter" type="submit" color="Primary" link=""
                            classname="px-5 py-[0.4rem] mx-4 rounded-lg text-base" icons="" />
                        </div>

                        </div>
                        
                    </div>

                </form>
                <div id="map" class="border h-full rounded-lg bg-white drop-shadow-sm"></div>
            </div>

        </div>
        @include('partials/footer')

    </div>

    <script src="{{ secure_asset('build/assets/app-Wo4miWF5.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>

    <script>
        function initMap() {

            const data = <?php echo json_encode($data); ?>;
            const categories = <?php echo json_encode($categories); ?>;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        map.setCenter(pos);
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
            }

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
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

            data.forEach((item) => {
                let iconUrl;
                if (item.tinggi >= categories[0].tinggi_minimal && item.tinggi < categories[0].tinggi_maksimal) {
                    iconUrl = "{{ asset('storage/icons/icon_1.png') }}";
                } else if (item.tinggi >= categories[1].tinggi_minimal && item.tinggi < categories[1]
                    .tinggi_maksimal) {
                    iconUrl = "{{ asset('storage/icons/icon_2.png') }}";
                } else if (item.tinggi >= categories[2].tinggi_minimal && item.tinggi < categories[2]
                    .tinggi_maksimal) {
                    iconUrl = "{{ asset('storage/icons/icon_3.png') }}";
                } else if (item.tinggi >= categories[3].tinggi_minimal && item.tinggi < categories[3]
                    .tinggi_maksimal) {
                    iconUrl = "{{ asset('storage/icons/icon_4.png') }}";
                } else {
                    iconUrl = "{{ asset('storage/icons/icon_5.png') }}";
                }

                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(item.latitude),
                        lng: parseFloat(item.longitude),
                    },
                    map: map,
                    // icon: {
                    //     path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -5,-30 A 5,5 0 1,1 5,-30 A 5,5 0 1,1 -5,-30',
                    //     fillColor: color,
                    //     fillOpacity: 1,
                    //     strokeColor: '#000',
                    //     strokeWeight: 1,
                    //     scale: 1
                    // }
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(20, 28),
                    }
                });

                var tempContentString = `
                <div style="padding: 2px; display: flex; flex-direction: row; max-width: 800px;">
                `

                if (item.foto) {
                    item.foto = 'storage/' + item.foto;
                    tempContentString += `
                        <img src="{{ asset('${item.foto}') }}" style="max-width: 150px; height: auto; border-radius: 6px; margin-right: 8px;">
                    `
                }

                const contentString = tempContentString + `
                        <div style="display: flex; flex-direction: column; justify-content: center;">
                            <p style="margin: 0; color: #4d4d4d; font-size: 16px; font-weight: 400; padding: 12px 0;">Ketinggian banjir : <span style="color : #0FB92A;">${item.tinggi}</span></p>
                            <p style="margin: 0; color: #4d4d4d; font-size: 16px; font-weight: 400;"><span style="font-style: italic;  font-weight: 300;">Dicatat Oleh</span> : ${item.user.name}</p>
                        </div>
                </div>
                `;

                const infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });

                marker.addListener('mouseover', function() {
                    infowindow.open(map, marker);
                });

                marker.addListener('mouseout', function() {
                    infowindow.close()
                });
            });
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3j--iDhYGf5VEqBQBTSF46W1nBDKqgfk&callback=initMap&loading=async"
        async defer></script>

</body>

</html>
