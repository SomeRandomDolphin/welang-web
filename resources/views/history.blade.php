@extends('layouts/main')

@section('container')
    <div class="py-10 px-6 w-screen bg-[#F8FCFF]">
        <div class="fRow flex-col sm:flex-row items-start sm:items-center gap-3 justify-between">
            <div>
                <h1 class="font-bold text-2xl">Riwayat Entri</h1>
                <p class="font-light my-2 text-base text-Inactive">Berikut adalah seluruh entri data laporan genangan yang telah
                    tercatat pada
                    sistem.</p>
            </div>
            <div class="w-full sm:w-auto">   
                    <a href="{{ route('export', ['search' => $filter['search'], 'start' => $filter['start_date'], 'end' => $filter['end_date']]) }}" 
                        class="flex justify-center gap-x-4 px-6 py-3 items-center rounded-md w-full sm:w-fit text-white" style="background-color: black">
                        Download
                    </a>

            </div>
        </div>

        <form class="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm" id="searchForm"
            action="{{ route('history') }}" method="GET">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                <div class="lg:col-span-3">
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-6">Cari
                        Petugas</label>
                    <input id="search" name="search" type="text" placeholder="Cari nama petugas"
                        value="{{ $filter['search'] ?? '' }}"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="lg:col-span-6">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Rentang
                        Tanggal</label>
                    <div class="grid grid-cols-1 sm:grid-cols-[auto,minmax(0,1fr),auto,minmax(0,1fr)] items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 p-2">
                        <div class="flex items-center gap-2 px-2">
                            <img src="{{ secure_asset('./calendar.svg') }}" class="w-4 h-4 shrink-0" />
                            <span class="text-xs text-slate-500">Dari</span>
                        </div>
                        <input type="text" name="start" autocomplete="off" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy"
                            placeholder="dd/mm/yyyy"
                            class="min-w-0 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            value="{{ $filter['start_date'] ?? '' }}">

                        <span class="hidden sm:block text-slate-400">sampai</span>

                        <div class="flex items-center gap-2 px-2 sm:hidden">
                            <span class="text-xs text-slate-500">Sampai</span>
                        </div>
                        <input type="text" name="end" autocomplete="off" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy"
                            placeholder="dd/mm/yyyy"
                            class="min-w-0 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            value="{{ $filter['end_date'] ?? '' }}">
                    </div>
                </div>

                <div class="lg:col-span-3 flex gap-2 lg:justify-end">
                    <a href="{{ route('history') }}"
                        class="lg:w-auto lg:min-w-[110px] flex-shrink-0 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                    <button type="submit"
                        class="lg:w-auto lg:min-w-[110px] flex-shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Filter
                    </button>
                </div>
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
                        {{-- <th scope="col" class="thead">
                            Koordinat Lokasi
                        </th> --}}
                        <th scope="col" class="thead">
                            Tinggi Genangan
                        </th>
                        <th scope="col" class="thead">
                            Foto
                        </th>
                        @if (Auth::check() && Auth::user()->is_admin)
                            <th scope="col" class="thead">
                                Aksi
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody class="h-full">
                    @foreach ($data as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 hover:bg-gray-50 text-center">
                            <th scope="row" class="tdata">
                                {{ $item->user['name'] }}
                            </th>
                            <td class="tdata">
                                {{ \Carbon\Carbon::parse($item['tanggal_kejadian'])->format('d M Y') }}
                            </td>
                            {{-- <td class="tdata">
                                {{ $item['latitude'] }}, {{ $item['longitude'] }}
                            </td> --}}
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
                            @if (Auth::check() && Auth::user()->is_admin)
                                <td class="tdata">
                                    <a href="{{ route('admin.history.detail', $item->id) }}"
                                        class="inline-flex items-center justify-center rounded p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-700"
                                        title="Lihat detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path d="M10 3c4.72 0 8.61 3.18 9.71 7.5-1.1 4.32-4.99 7.5-9.71 7.5S1.39 14.82.29 10.5C1.39 6.18 5.28 3 10 3Zm0 3.5a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm0 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.history.delete', $item->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus entri ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded p-2 text-red-600 hover:bg-red-50 hover:text-red-700"
                                            title="Hapus entri">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7.707 4h4.586l.707 1H7l.707-1zM5 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm3-1a1 1 0 012 0v7a1 1 0 11-2 0V7z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
