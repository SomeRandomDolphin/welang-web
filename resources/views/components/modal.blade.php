@props(['categories'])

<div id="iconUpdate-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="kategori">
                    Upload Icon Baru untuk Kategori
                </h3>
                <button type="button"
                    class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="iconUpdate-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="px-4 pb-4">
                <form class="space-y-4" action="{{ route('saveCategory') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="">
                        <label for="image" class="pFormActive">Icon</label>
                        <label for="image"
                            class="flex flex-col items-center justify-center w-full min-h-24 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 hover:cursor-pointer">
                            <img src="./camera.svg" alt="image" class="max-h-64 h-fit rounded-lg" id="file-preview">
                            <p id="file-preview-title"></p>
                            <input id="image" type="file" class="hidden" name="image"
                                onchange="previewImage(event);" />
                        </label>
                    </div>

                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white ">
                        Pilih kategori
                    </label>

                    <select id="category" name="category"
                        class="hover:bg-gray-100 hover:cursor-pointer border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected>Pilih Kategori</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                    </select>

                    <div class="mb-6">
                        <label for="min_height"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tinggi Minimal Dalam
                            Cm</label>
                        <input type="number" id="min_height" name="min_height"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="max_height"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tinggi Maksimal Dalam
                            Cm</label>
                        <input type="number" id="max_height" name="max_height"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <button type="submit"
                        class="w-full text-white buttonActive hover:bg-slate-950 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Perbarui
                        Kategori</button>

                </form>
            </div>
        </div>
    </div>
</div>

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

    const select = document.getElementById('category');

    select.addEventListener('change', function() {
        const selectedIndex = this.selectedIndex - 1;
        const selectedCategory = <?php echo json_encode($categories); ?>[selectedIndex];

        document.getElementById('min_height').value = selectedCategory['tinggi_minimal'];
        document.getElementById('max_height').value = selectedCategory['tinggi_maksimal'];
    });
</script>
