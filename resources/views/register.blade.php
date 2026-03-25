@extends('layouts/auth')

@section('container')
    <form action="{{ url('/register') }}" method="POST"
        class="w-[90%] lg:w-[35%] border bg-white flex flex-col justify-between items-center text-center px-8 py-8 rounded-lg drop-shadow-md gap-y-3">
        @csrf

        @if (session('success'))
            <div class="w-full bg-green-500 text-white px-4 py-3 rounded-lg text-sm" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('failed'))
            <div class="w-full bg-red-500 text-white px-4 py-3 rounded-lg text-sm" role="alert">
                {{ session('failed') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="w-full bg-red-500 text-white px-4 py-3 rounded-lg text-sm text-left" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <img src={{ secure_asset('/login.svg') }} alt="icon" class="h-10">
        <h2 class="h2Form">Daftar Akun</h2>
        <p class="pFormInactive font-light pb-2">Pilih metode pendaftaran lalu isi data akun.</p>

        <x-forms.input classname="w-full" label="Nama Lengkap" placeholder="Nama" name="name" type="text"
            value="{{ old('name') }}" />

        <div class="w-full flex flex-col items-start">
            <label class="block mb-2 pFormActive">Daftar Dengan</label>
            <div class="w-full grid grid-cols-2 gap-2">
                <button type="button" id="register-email-tab"
                    class="cursor-pointer rounded-lg border border-gray-200 px-3 py-2 text-sm text-center text-gray-600">
                    Email
                </button>
                <button type="button" id="register-phone-tab"
                    class="cursor-pointer rounded-lg border border-gray-200 px-3 py-2 text-sm text-center text-gray-600">
                    No. HP
                </button>
            </div>
        </div>

        <div id="register-email-field" class="w-full">
            <x-forms.input classname="w-full" label="Email" placeholder="nama@gmail.com" name="email" type="text"
                value="{{ old('email') }}" />
        </div>

        <div id="register-phone-field" class="w-full hidden">
            <div class="my-1 flex flex-col items-start w-full">
                <label for="phone_number" class="block mb-2 pFormActive">No. HP</label>
                <div class="w-full flex items-center border border-gray-200 rounded-lg overflow-hidden">
                    <span class="px-3 py-2 bg-gray-50 text-gray-600 border-r border-gray-200">+62</span>
                    <input type="text" placeholder="8123456789" name="phone_number" id="phone_number"
                        inputmode="numeric" pattern="[0-9]*" value="{{ preg_replace('/^\+62/', '', old('phone_number', '')) }}"
                        class="w-full pFormActive font-light focus:ring-blue-500 focus:border-blue-500 block py-2 px-3 border-0">
                </div>
            </div>
        </div>

        <x-forms.input classname="w-full" label="Kata Sandi" placeholder="Masukkan kata sandi" name="password"
            type="password" value="" />

        <x-forms.input classname="w-full" label="Konfirmasi Kata Sandi" placeholder="Masukkan kata sandi" name="password_confirmation"
            type="password" value="" />

        <x-button message="Daftar" type="submit" color="Primary" link="" type="submit"
            classname="w-full my-2 py-[10px] text-base" icons="" />
    </form>

    <script>
        (function() {
            const emailField = document.getElementById('register-email-field');
            const phoneField = document.getElementById('register-phone-field');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone_number');
            const emailTab = document.getElementById('register-email-tab');
            const phoneTab = document.getElementById('register-phone-tab');
            let method = phoneInput && phoneInput.value ? 'phone' : 'email';

            function syncRegisterFields() {
                const isEmail = method === 'email';

                emailField.classList.toggle('hidden', !isEmail);
                phoneField.classList.toggle('hidden', isEmail);

                emailTab.classList.toggle('bg-blue-50', isEmail);
                emailTab.classList.toggle('border-blue-500', isEmail);
                emailTab.classList.toggle('text-blue-700', isEmail);
                phoneTab.classList.toggle('bg-blue-50', !isEmail);
                phoneTab.classList.toggle('border-blue-500', !isEmail);
                phoneTab.classList.toggle('text-blue-700', !isEmail);

                if (isEmail && phoneInput) {
                    phoneInput.value = '';
                }

                if (!isEmail && emailInput) {
                    emailInput.value = '';
                }
            }

            emailTab.addEventListener('click', function() {
                method = 'email';
                syncRegisterFields();
            });

            phoneTab.addEventListener('click', function() {
                method = 'phone';
                syncRegisterFields();
            });

            syncRegisterFields();
        })();
    </script>
@endsection
