<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ secure_asset('/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const data = <?php echo json_encode($data); ?>;
        const categories = <?php echo json_encode($categories); ?>;
        const assetBase = "{{ asset('') }}";

        const defaultCenter = data.length ?
            [parseFloat(data[0].latitude), parseFloat(data[0].longitude)] :
            [-7.2575, 112.7521];

        const map = L.map("map", {
            zoomControl: true,
        }).setView(defaultCenter, 12);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = [position.coords.latitude, position.coords.longitude];
                    map.setView(pos, 12);
                },
                () => {
                    handleLocationError(true);
                }
            );
        } else {
            handleLocationError(false);
        }

        function handleLocationError(browserHasGeolocation) {
            const errorMessage = browserHasGeolocation ?
                "Error: The Geolocation service failed." :
                "Error: Your browser doesn't support geolocation.";
            console.error(errorMessage);
        }

        function resolveIconUrl(item) {
            if (!categories.length) {
                return "{{ asset('storage/icons/icon_1.png') }}";
            }

            if (item.tinggi >= categories[0].tinggi_minimal && item.tinggi < categories[0].tinggi_maksimal) {
                return "{{ asset('storage/icons/icon_1.png') }}";
            }
            if (item.tinggi >= categories[1].tinggi_minimal && item.tinggi < categories[1].tinggi_maksimal) {
                return "{{ asset('storage/icons/icon_2.png') }}";
            }
            if (item.tinggi >= categories[2].tinggi_minimal && item.tinggi < categories[2].tinggi_maksimal) {
                return "{{ asset('storage/icons/icon_3.png') }}";
            }
            if (item.tinggi >= categories[3].tinggi_minimal && item.tinggi < categories[3].tinggi_maksimal) {
                return "{{ asset('storage/icons/icon_4.png') }}";
            }

            return "{{ asset('storage/icons/icon_5.png') }}";
        }

        function isValidPhotoPath(path) {
            return typeof path === 'string' &&
                /^[a-zA-Z0-9_\-\/\.]+$/.test(path) &&
                !/\.\./.test(path) &&
                !/^\//.test(path);
        }

        function buildPopupContent(item) {
            let content =
                "<div style=\"padding: 2px; display: flex; flex-direction: row; max-width: 800px;\">";

            if (item.foto && isValidPhotoPath(item.foto)) {
                const photoUrl = assetBase + "storage/" + item.foto;
                content +=
                    `<img src="${photoUrl}" style="max-width: 150px; height: auto; border-radius: 6px; margin-right: 8px;">`;
            }

            content +=
                `<div style="display: flex; flex-direction: column; justify-content: center;">
                    <p style="margin: 0; color: #4d4d4d; font-size: 16px; font-weight: 400; padding: 12px 0;">
                        Ketinggian banjir : <span style="color : #0FB92A;">${item.tinggi}</span>
                    </p>
                    <p style="margin: 0; color: #4d4d4d; font-size: 16px; font-weight: 400;">
                        <span style="font-style: italic; font-weight: 300;">Dicatat Oleh</span> : ${item.user.name}
                    </p>
                </div>
            </div>`;

            return content;
        }

        data.forEach((item) => {
            const iconUrl = resolveIconUrl(item);
            const marker = L.marker([
                parseFloat(item.latitude),
                parseFloat(item.longitude),
            ], {
                icon: L.icon({
                    iconUrl,
                    iconSize: [20, 28],
                    iconAnchor: [10, 28],
                    popupAnchor: [0, -28],
                }),
            }).addTo(map);

            const popupContent = buildPopupContent(item);
            marker.bindPopup(popupContent);

            marker.on("click", () => marker.openPopup());
            marker.on("mouseover", () => marker.openPopup());
            marker.on("mouseout", () => marker.closePopup());
        });
    </script>

</body>

</html>
