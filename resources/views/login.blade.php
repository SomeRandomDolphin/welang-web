@extends('layouts/auth')

@if (session('success'))
    <div class="bg-green-500 text-white px-6 py-4 border-0 rounded relative mb-4" role="alert">
        <span class="text-xl inline-block mr-5 align-middle">
            <i class="fas fa-check-circle"></i>
        </span>
        <span class="inline-block align-middle mr-8">
            {{ session('success') }}
        </span>
        <button
            class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
            onclick="this.parentElement.style.display='none';">
            <span>×</span>
        </button>
    </div>
@endif

@if (session('failed'))
    <div class="bg-red-500 text-white px-6 py-4 border-0 rounded relative mb-4" role="alert">
        <span class="text-xl inline-block mr-5 align-middle">
            <i class="fas fa-exclamation-circle"></i>
        </span>
        <span class="inline-block align-middle mr-8">
            {{ session('failed') }}
        </span>
        <button
            class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none"
            onclick="this.parentElement.style.display='none';">
            <span>×</span>
        </button>
    </div>
@endif

@section('container')
    <form action="{{ url('/login') }}" method="POST"
        class="w-[90%] lg:w-[35%] h-[60%] border bg-white flex flex-col justify-between items-center text-center px-8 py-12 rounded-lg drop-shadow-md">
        @csrf
        <img src={{ secure_asset('/login.svg') }} alt="icon" class="h-10">
        <h2 class="h2Form">Masuk dengan email anda</h2>
        <p class="pFormInactive font-light">Masukkan email dan kata sandi yang telah terdaftar.</p>

        <x-forms.input classname="w-full" label="Email" placeholder="Nama@gmail.com" name="email" type="text"
            value="" />

        <x-forms.input label="Kata Sandi" classname="w-full" placeholder="Masukkan kata sandi" name="password"
            type="password" value="" />

        <x-button message="Masuk" type="submit" color="Primary" link="" classname="w-full my-2 py-[10px] text-base"
            icons="" />
    </form>
@endsection
