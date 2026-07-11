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
                
                <div class="flex items-stretch gap-x-2">
                <form id="filterForm" action="{{ route('dashboard') }}" method="GET"
                    class="flex-1 border h-auto md:h-[56px] rounded-lg bg-white flex flex-wrap justify-evenly md:justify-between items-center px-3 md:px-6 py-2 md:py-0 drop-shadow-sm gap-y-2 md:gap-y-0">

                    <h2 class="text-sm text-graySecondary hidden md:block">Filter dan Lokasi</h2>

                    <div class="flex flex-col md:flex-row text-grayPrimary items-center md:gap-x-4">
                        <div class="flex flex-col md:flex-row items-center gap-y-1">
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
                        

                        <div class="flex flex-col justify-start items-center md:flex-row md:gap-x-2">
                        <h3 class="textFilter font-medium">TINGGI</h3>
                        <div class="flex justify-center items-center gap-x-3">
                            <input name="min" type="number" id="heightInput"
                                class="border rounded-lg border-slate-500/20 text-black text-center focus:ring-0 focus:border-none pFormActive font-light w-16"
                                placeholder="0" min="0" max="100" value="{{ $filter['min_height'] ?? '' }}">

                            <p>-</p>

                            <input name="max" type="number" id="heightInput"
                                class="border rounded-lg border-slate-500/20 text-black text-center focus:ring-0 focus:border-none pFormActive font-light w-16"
                                placeholder="0" min="0" max="100" value="{{ $filter['max_height'] ?? '' }}">
                            <p class="text-gray-500 font-light">cm</p>

                        <x-button message="Filter" type="submit" color="Primary" link=""
                            classname="px-4 py-[0.4rem] rounded-lg text-sm" icons="" />
                        </div>

                        </div>
                        
                    </div>

                </form>
            </div>
            <div>
                <h2 class="md:hidden text-center font-semibold text-gray-800 px-1">Informasi titik-titik Genangan/Banjir</h2>
            </div>
                <div class="relative">
                    <div id="map" class="border min-h-[50vh] md:h-full rounded-lg bg-white drop-shadow-sm"></div>
                    <div class="md:hidden absolute bottom-5 right-0 z-[500] w-[10rem] rounded-lg border border-gray-200 bg-white/95 p-3 shadow-sm backdrop-blur-sm">
                        <p class="text-[11px] font-semibold tracking-wide text-gray-700 uppercase">Legenda Kedalaman</p>
                        <div class="mt-2 max-h-36 space-y-1.5 overflow-y-auto pr-1 text-[10px] text-gray-700">
                            @foreach ($categories as $item)
                                <div class="flex items-start gap-2 leading-tight">
                                    <img src="{{ $item['ikon'] }}" alt="Ikon kategori {{ $item['jenis'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 object-contain">
                                    <span>Kategori {{ $item['jenis'] }}: {{ $item['tinggi_minimal'] }} - {{ $item['tinggi_maksimal'] }} cm</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include('partials/footer')

    </div>

    <script src="{{ secure_asset('build/assets/app-Wo4miWF5.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const data = @json($data);
        const categories = @json($categories);
        const assetBase = "{{ asset('') }}";

        const lastDataPoint = data.length ? data[data.length - 1] : null;
        const defaultCenter = lastDataPoint ?
            [parseFloat(lastDataPoint.latitude), parseFloat(lastDataPoint.longitude)] :
            [-7.2575, 112.7521];

        const map = L.map("map", {
            zoomControl: true,
        }).setView(defaultCenter, 12);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        function handleLocationError(browserHasGeolocation) {
            const errorMessage = browserHasGeolocation ?
                "Error: The Geolocation service failed." :
                "Error: Your browser doesn't support geolocation.";
            console.error(errorMessage);
        }

        function resolveCategory(item) {
            if (!categories.length) {
                return null;
            }

            const height = Number(item.tinggi);
            const orderedCategories = [...categories].sort(
                (a, b) => Number(a.tinggi_minimal) - Number(b.tinggi_minimal)
            );

            const exactMatch = orderedCategories.find((category, index) => {
                const isLastCategory = index === orderedCategories.length - 1;
                return height >= Number(category.tinggi_minimal) &&
                    (height < Number(category.tinggi_maksimal) ||
                        (isLastCategory && height >= Number(category.tinggi_minimal)));
            });

            return exactMatch ?? orderedCategories[0];
        }

        function resolveAssetUrl(path) {
            if (typeof path !== "string" || !path.trim()) {
                return assetBase + "icons/icon_1.png";
            }

            if (/^(https?:)?\/\//i.test(path) || path.startsWith("data:")) {
                return path;
            }

            return assetBase + path.replace(/^\/+/, "");
        }

        function escapeHtml(value) {
            const element = document.createElement("div");
            element.textContent = value == null ? "" : String(value);
            return element.innerHTML;
        }

        function isValidPhotoPath(path) {
            return typeof path === 'string' &&
                /^[a-zA-Z0-9_\-\/\.]+$/.test(path) &&
                !/\.\./.test(path) &&
                !/^\//.test(path);
        }

        function buildPopupContent(item, category) {
            const height = escapeHtml(item.tinggi);
            const categoryName = category ? escapeHtml(category.jenis) : "-";
            const reporterName = escapeHtml(item.user?.name ?? "Tidak diketahui");
            const notes = typeof item.catatan === "string" ? item.catatan.trim() : "";
            let content =
                "<div style=\"padding: 4px; display: flex; flex-direction: row; align-items: flex-start; width: 100%; max-width: 280px; gap: 8px; box-sizing: border-box;\">";

            if (item.foto && isValidPhotoPath(item.foto)) {
                const photoUrl = assetBase + "storage/" + item.foto;
                content +=
                    `<img src="${photoUrl}" style="width: 96px; max-width: 96px; height: auto; border-radius: 6px; flex-shrink: 0;">`;
            }

            content +=
                `<div style="display: flex; flex-direction: column; justify-content: center; min-width: 0; width: 100%; overflow-wrap: anywhere; word-break: break-word;">
                    <p style="margin: 0; color: #4d4d4d; font-size: 14px; font-weight: 400; line-height: 1.45; padding: 2px 0 8px;">
                        Ketinggian banjir : <span style="color : #0FB92A;">${height} cm</span>
                    </p>
                    <p style="margin: 0; color: #4d4d4d; font-size: 14px; font-weight: 400; line-height: 1.45; padding-bottom: 8px;">
                        Kategori : <span style="color: #0FB92A;">${categoryName}</span>
                    </p>
                    <p style="margin: 0; color: #4d4d4d; font-size: 14px; font-weight: 400; line-height: 1.45; overflow-wrap: anywhere; word-break: break-word;">
                        <span style="font-style: italic; font-weight: 300;">Dicatat Oleh</span> : ${reporterName}
                    </p>
                    ${notes ? `<p style="margin: 8px 0 0; color: #4d4d4d; font-size: 14px; font-weight: 400; line-height: 1.45; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word;"><span style="font-weight: 500;">Catatan:</span> ${escapeHtml(notes)}</p>` : ""}
                </div>
            </div>`;

            return content;
        }

        data.forEach((item) => {
            const category = resolveCategory(item);
            const iconUrl = resolveAssetUrl(category?.ikon);
            const marker = L.marker([
                parseFloat(item.latitude),
                parseFloat(item.longitude),
            ], {
                icon: L.icon({
                    iconUrl,
                    // Match the balanced icon presentation used by the category
                    // legend instead of the source PNG's tall canvas ratio.
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40],
                }),
            }).addTo(map);

            const popupContent = buildPopupContent(item, category);
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                minWidth: 220,
            });

            marker.on("click", () => marker.openPopup());
            marker.on("mouseover", () => marker.openPopup());
            marker.on("mouseout", () => marker.closePopup());
        });
    </script>

</body>

</html>
