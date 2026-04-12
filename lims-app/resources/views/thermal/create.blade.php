<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Suhu Pemanasan - LIMS RT-PCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6 sm:p-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Input Suhu Pemanasan</h1>
                <p class="text-gray-600 text-sm mt-1">Extraction-Free RT-PCR</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg" role="alert">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('thermal.store') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="sample_id" class="block text-sm font-medium text-gray-700 mb-2">
                        ID Sampel <span class="text-red-500">*</span>
                    </label>
                    <select id="sample_id" name="sample_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('sample_id') border-red-500 focus:ring-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Sampel --</option>
                        @foreach ($samples as $sample)
                            <option value="{{ $sample->id }}" @selected(old('sample_id') == $sample->id)>
                                {{ $sample->sample_code }} - {{ $sample->patient_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sample_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ temperature: '', isLow: false, isOptimal: false, isHigh: false, isExceeded: false, showInvalidCharWarning: false }">
                    <label for="temperature_celsius" class="block text-sm font-medium text-gray-700 mb-2">
                        Suhu Pemanasan (°C) <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input type="text" id="temperature_celsius" name="temperature_celsius"
                            @input="
                                let originalValue = $event.target.value;
                                let sanitizedValue = originalValue.replace(/[^0-9.,-]/g, '');

                                /* Munculkan peringatan jika ada huruf/simbol yang diblokir */
                                if (originalValue !== sanitizedValue) {
                                    showInvalidCharWarning = true;
                                    setTimeout(() => { showInvalidCharWarning = false }, 3000);
                                }

                                /* Kembalikan input ke angka yang sudah dibersihkan */
                                $event.target.value = sanitizedValue;
                                
                                /* Logika status warna untuk rentang 95-98 */
                                let tempCheck = sanitizedValue.replace(',', '.');
                                let temp = parseFloat(tempCheck);
                                
                                isLow = temp < 95 && temp !== '';
                                isOptimal = temp >= 95 && temp <= 98;
                                isHigh = temp > 98 && temp !== '';
                                isExceeded = temp < 95 || temp > 98;
                                temperature = tempCheck;
                            "
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('temperature_celsius')
border-red-500 focus:ring-red-500
@enderror"
                            placeholder="Masukkan suhu (contoh: 95.0)" value="{{ old('temperature_celsius') }}"
                            required />
                        <div class="absolute right-3 top-3">
                            <svg x-show="isOptimal" class="h-5 w-5 text-green-600 animate-pulse" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg x-show="isLow || isHigh" class="h-5 w-5 text-red-600 animate-pulse" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.364zm1.414-1.414A8 8 0 112.828 2.828a8 8 0 0111.314 11.314z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg x-show="!isOptimal && !isLow && !isHigh" class="h-5 w-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <p x-show="showInvalidCharWarning" style="display: none;" x-transition
                        class="text-sm text-red-500 font-semibold mt-1">
                        ⚠️ Hanya format angka yang diizinkan!
                    </p>

                    <div class="mt-2 space-y-2">
                        <p x-show="isOptimal" class="text-sm text-green-600 flex items-center font-semibold">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            ✓ Suhu optimal untuk RT-PCR (95-98°C)
                        </p>

                        <p x-show="isLow" class="text-sm text-red-600 flex items-center font-semibold">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.364zm1.414-1.414A8 8 0 112.828 2.828a8 8 0 0111.314 11.314z"
                                    clip-rule="evenodd" />
                            </svg>
                            ❌ Suhu terlalu rendah! Minimal 95°C
                        </p>

                        <p x-show="isHigh" class="text-sm text-red-600 flex items-center font-semibold">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.364zm1.414-1.414A8 8 0 112.828 2.828a8 8 0 0111.314 11.314z"
                                    clip-rule="evenodd" />
                            </svg>
                            ❌ Suhu terlalu tinggi! Maksimal 98°C
                        </p>
                    </div>

                    @error('temperature_celsius')
                        <div
                            style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-top: 10px;">
                            <strong>❌ Gagal Disimpan:</strong> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 8a1 1 0 000 2h6a1 1 0 100-2H8z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm text-blue-700">
                            <strong>Perhatian:</strong> Rentang suhu yang optimal untuk RT-PCR adalah <strong>95°C -
                                98°C</strong>.
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full mt-6 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Data Suhu
                </button>
            </form>
            <!-- Histori Pemanasan Sampel -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Histori Pemanasan Sampel</h2>

                @if ($thermalLogs->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada data suhu yang diinput</h3>
                        <p class="text-sm text-gray-500">Silakan masukkan data pertama Anda di form di atas.</p>
                    </div>
                @else
                    <!-- Tabel Histori -->
                    <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID Sampel</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Pasien</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Suhu (°C)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Waktu Input</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($thermalLogs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $log->sample->sample_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->sample->patient_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($log->temperature_celsius >= 95 && $log->temperature_celsius <= 98)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ number_format($log->temperature_celsius, 2) }}°C ✓
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ number_format($log->temperature_celsius, 2) }}°C 🚨
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Info (opsional, jika diperlukan di masa depan) -->
                    <div class="mt-4 text-sm text-gray-600">
                        Menampilkan {{ $thermalLogs->count() }} data terbaru.
                    </div>
                @endif
            </div>
            <!-- Footer Help -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-600 text-center">
                    Sistem validasi real-time untuk RT-PCR dengan rentang suhu optimal 95-98°C.
                </p>
            </div>
        </div>

    </div>
</body>

</html>
