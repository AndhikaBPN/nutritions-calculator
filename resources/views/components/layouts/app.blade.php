<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'NutriLens') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[#F0F7F4]">

    {{-- Navbar --}}
    <header class="bg-[#F0F7F4] sticky top-0 z-40 border-b border-[#E0EEE8]">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">

            <a href="{{ route('home') }}" class="text-xl font-bold text-[#2D6A4F]">NutriLens</a>

            <nav class="flex items-center bg-white/60 rounded-full p-1 gap-0.5 shadow-sm">
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'bg-white shadow text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' }} px-6 py-1.5 rounded-full text-sm transition-all">
                    Home
                </a>
                <a href="{{ route('history') }}"
                   class="{{ request()->routeIs('history') ? 'bg-white shadow text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' }} px-6 py-1.5 rounded-full text-sm transition-all">
                    Histori
                </a>
                <a href="{{ route('profile') }}"
                   class="{{ request()->routeIs('profile') ? 'bg-white shadow text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700' }} px-6 py-1.5 rounded-full text-sm transition-all">
                    Profile
                </a>
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="bg-[#2D6A4F] hover:bg-[#245740] text-white px-5 py-2 rounded-full text-sm font-semibold transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </header>

    {{-- Upload Modal --}}
    <div id="upload-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeUploadModal()">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900">Upload Foto Makanan</h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('meal-logs.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Makan</label>
                    <select id="modal-meal-type" name="meal_type"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]">
                        <option value="pagi">Makan Pagi</option>
                        <option value="siang">Makan Siang</option>
                        <option value="malam">Makan Malam</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Makanan</label>
                    <label id="photo-drop-zone"
                           class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#2D6A4F] hover:bg-[#F0F7F4] transition-colors group">
                        <div id="photo-placeholder" class="text-center">
                            <div class="text-3xl text-gray-300 group-hover:text-[#2D6A4F] mb-1">&#128247;</div>
                            <p class="text-sm text-gray-400 group-hover:text-[#2D6A4F]">Klik untuk pilih foto</p>
                            <p class="text-xs text-gray-300 mt-0.5">JPG, PNG, WEBP</p>
                        </div>
                        <img id="photo-preview" src="" alt="Preview" class="hidden h-full w-full object-cover rounded-xl">
                        <input id="photo-input" type="file" name="photo" accept="image/*" required class="hidden"
                               onchange="previewPhoto(this)">
                    </label>
                </div>

                <input type="hidden" name="date" value="{{ now()->toDateString() }}">

                <button type="submit"
                        class="w-full py-2.5 bg-[#2D6A4F] hover:bg-[#245740] text-white font-semibold rounded-xl text-sm transition-colors">
                    Upload &amp; Analisis
                </button>
            </form>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="max-w-6xl mx-auto px-6 pt-4">
        @if (session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 mb-2">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 mb-2">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <main class="max-w-6xl mx-auto px-6 py-6">
        {{ $slot }}
    </main>

    <script>
        function openUploadModal(mealType) {
            document.getElementById('upload-modal').classList.remove('hidden');
            if (mealType) {
                document.getElementById('modal-meal-type').value = mealType;
            }
        }
        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
        }
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const preview = document.getElementById('photo-preview');
                    const placeholder = document.getElementById('photo-placeholder');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
