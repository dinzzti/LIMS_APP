<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Inaktivasi Termal - LIMS RT-PCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">

            {{-- Flash message dari thermal.complete --}}
            @if (session('success'))
                <div
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586
                   7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        🧪 Dasbor Antrean Termal
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">
                        Jalur Inaktivasi Termal — Extraction-Free RT-PCR
                    </p>
                </div>
                <!-- Tombol ke form input suhu (US 2.2) -->
                <a href="{{ route('thermal.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    + Input Suhu Pemanasan
                </a>
            </div>

            <!-- Statistik Ringkas -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-3xl font-bold text-yellow-500">
                        {{ $waitingQueue->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Menunggu</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-3xl font-bold text-blue-500">
                        {{ $processingQueue->count() }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Sedang Diproses</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-3xl font-bold text-green-500">
                        {{ $completedToday }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Selesai Hari Ini</p>
                </div>
            </div>

            <!-- Antrean: Menunggu Pemanasan -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
                    <h2 class="text-base font-semibold text-gray-800">
                        Menunggu Pemanasan
                    </h2>
                    <span class="ml-auto text-xs text-gray-400">
                        {{ $waitingQueue->total() }} sampel
                    </span>
                </div>

                @if ($waitingQueue->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                        Tidak ada sampel dalam antrean termal saat ini.
                    </div>
                @else
                    <div class="flex justify-between items-center p-4">
                        <form method="GET" action="{{ route('thermal.queue') }}" class="flex items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama pasien..."
                                class="border-gray-300 rounded-lg text-sm px-3 py-1 w-48">

                            <button type="submit"
                                class="px-3 py-1 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                                Cari
                            </button>

                        </form>
                        <form method="GET" action="{{ route('thermal.queue') }}">
                            <label class="text-sm text-gray-600 mr-2">Tampilkan:</label>
                            <select name="per_page" onchange="this.form.submit()"
                                class="border-gray-300 rounded-lg text-sm px-2 py-1">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            </select>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode
                                        Sampel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Pasien</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Terdaftar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($waitingQueue as $sample)
                                    <tr class="hover:bg-yellow-50 transition">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ $sample->sample_code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sample->patient_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            {{ $sample->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <!-- Tombol Mulai Proses → ke US 2.2 dengan sample_id pre-selected -->
                                            <a href="{{ route('thermal.create', ['sample_id' => $sample->id]) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                                                ▶ Mulai Proses
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $waitingQueue->links() }}
                    </div>
                @endif
            </div>

            <!-- Antrean: Sedang Diproses -->
            @if ($processingQueue->isNotEmpty())
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-400 inline-block animate-pulse"></span>
                        <h2 class="text-base font-semibold text-gray-800">
                            Sedang Diproses
                        </h2>
                        <span class="ml-auto text-xs text-gray-400">
                            {{ $processingQueue->count() }} sampel
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode
                                        Sampel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        Pasien</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($processingQueue as $sample)
                                    <tr class="bg-blue-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ $sample->sample_code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sample->patient_name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                                                Sedang Dipanaskan
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            @php
                                                $latestLog = $sample->thermalLogs->first();
                                            @endphp

                                            @if ($latestLog)
                                                <a href="{{ route('thermal.timer', $latestLog->id) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                                    ⏱ Lihat Timer
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Tabel: Siap Uji PCR (US 2.5) --}}
            @if ($readyPcrQueue->isNotEmpty())
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                        <h2 class="text-base font-semibold text-gray-800">
                            Siap Uji PCR
                        </h2>
                        <span class="ml-auto text-xs text-gray-400">
                            {{ $readyPcrQueue->count() }} sampel hari ini
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Kode Sampel
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Nama Pasien
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Selesai Dipanaskan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($readyPcrQueue as $sample)
                                    <tr class="hover:bg-green-50 transition">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ $sample->sample_code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sample->patient_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            {{ $sample->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                         text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Siap PCR
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Footer info -->
            <p class="text-center text-xs text-gray-400 mt-4">
                Halaman ini hanya menampilkan sampel jalur pemanasan termal.
                Jalur reguler ditangani di modul terpisah.
            </p>

        </div>
    </div>
</body>

</html>
