@extends('layouts/main')

@section('container')
    <div class="py-10 px-6 w-screen bg-[#F8FCFF]">
        <div class="fRow justify-between">
            <div>
                <h1 class="font-bold text-2xl">Riwayat Entri</h1>
                <p class="font-light my-2 text-base text-Inactive">Berikut adalah seluruh entri data survei yang telah
                    tercatat pada
                    sistem.</p>
            </div>
            <div>   
                    <a href="{{ route('export', ['search' => $filter['search'], 'start' => $filter['start_date'], 'end' => $filter['end_date']]) }}" 
                        class="flex justify-between gap-x-4 px-6 py-3 items-center rounded-md w-fit text-white" style="background-color: black">
                        Download
                    </a>

            </div>
        </div>

        <form class=" flex flex-col md:flex-row justify-between md:items-center" id="searchForm"
            action="{{ route('history') }}" method="GET">
            <x-forms.input label="" classname="w-full md:w-[40%] lg:w-[30%] text-sm"
                placeholder="Cari beberapa riwayat" name="search" type="text" value="{{ $filter['search'] ?? '' }}" />

            <div class="flex flex-row">
                <div class="p-0.5 w-[70%] md:w-full border rounded-md flex justify-center items-center right-0 bg-white">
                    <img src={{ secure_asset('./calendar.svg') }} class="mx-2" />
                    <input datepicker datepicker-format="yyyy-mm-dd" type="date" name="start" id="startDateInput"
                        class="border-none border-gray-200 pFormActive font-light w-full text-center focus:ring-0"
                        placeholder="Select date" value="{{ $filter['start_date'] ?? '' }}">
                    <p>-</p>
                    <input datepicker datepicker-format="yyyy-mm-dd" type="date" name="end" id="endDateInput"
                        class="border-none border-gray-200 pFormActive font-light rounded-lg w-full text-center focus:ring-0"
                        placeholder="Select date" value="{{ $filter['end_date'] ?? '' }}">
                </div>

                <x-button message="Filter" type="submit" color="Primary" link="" type="submit"
                    classname="w-full ml-4 py-2 px-2 text-base" icons="" />
            </div>
        </form>

        <div class="h-[h-nav] w-full max-h-[70%] min-h-[55%] overflow-y-scroll rounded-md no-scrollbar">
            <table class="w-full text-center text-base font-thin text-gray-500 rounded-md">
                <thead class="bg-[#E8F5FF] rounded-md">
                    <tr class="rounded-md">
                        <th scope="col" class="thead">
                            Nama Petugas
                        </th>
                        <th scope="col" class="thead">
                            Tanggal
                        </th>
                        <th scope="col" class="thead">
                            Koordinat Lokasi
                        </th>
                        <th scope="col" class="thead">
                            Tinggi Genangan
                        </th>
                        <th scope="col" class="thead">
                            Foto
                        </th>
                    </tr>
                </thead>

                <tbody class="h-full">
                    @foreach ($data as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 hover:bg-gray-50 text-center">
                            <th scope="row" class="tdata">
                                {{ $item->user['name'] }}
                            </th>
                            <td class="tdata">
                                {{ $item['tanggal_kejadian'] }}
                            </td>
                            <td class="tdata">
                                {{ $item['latitude'] }}, {{ $item['longitude'] }}
                            </td>
                            <td class="tflood">
                                {{ $item['tinggi'] }}
                            </td>
                            <td class="tdata hover:textblue">
                                @if ($item['foto'])
                                    <a class="hover:text-Active" href="storage/{{ $item['foto'] }}">Gambar Lokasi
                                        Kejadian</a>
                                @else
                                    <p>Tidak Ada Gambar</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
