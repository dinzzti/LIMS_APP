<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Pemanasan — {{ $thermalLog->sample->sample_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4" x-data="{
        totalSeconds: {{ $remainSeconds }},
        durationSeconds: {{ $thermalLog->duration_minutes * 60 }},
        interval: null,
        isFinished: {{ $remainSeconds <= 0 ? 'true' : 'false' }},
        showWarningPopup: {{ $remainSeconds <= 60 && $remainSeconds > 0 ? 'true' : 'false' }},
        audioCtx: null,
    
        get minutes() {
            return String(Math.floor(this.totalSeconds / 60)).padStart(2, '0');
        },
        get seconds() {
            return String(this.totalSeconds % 60).padStart(2, '0');
        },
        get progress() {
            if (this.durationSeconds === 0) return 100;
            return ((this.durationSeconds - this.totalSeconds) / this.durationSeconds) * 100;
        },
        get isWarning() {
            return this.totalSeconds <= 60 && !this.isFinished;
        },
    
        /*
         * Buat bunyi beep menggunakan Web Audio API.
         * Tidak membutuhkan file audio eksternal.
         * count = jumlah beep, interval = jeda antar beep (ms)
         */
        beep(count = 1, intervalMs = 300) {
            try {
                if (!this.audioCtx) {
                    this.audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                }
                for (let i = 0; i < count; i++) {
                    const oscillator = this.audioCtx.createOscillator();
                    const gainNode = this.audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(this.audioCtx.destination);
    
                    oscillator.type = 'square';
                    oscillator.frequency.value = 880; // Hz — nada peringatan
                    gainNode.gain.value = 0.3;
    
                    const startAt = this.audioCtx.currentTime + (i * intervalMs / 1000);
                    oscillator.start(startAt);
                    oscillator.stop(startAt + 0.18); // durasi satu beep
                }
            } catch (e) {
                console.warn('Web Audio API tidak tersedia:', e);
            }
        },
    
        /*
         * Alarm terus berbunyi setiap 10 detik selama popup aktif.
         * Tidak bisa dimatikan manual sebelum timer habis
         * (sesuai aturan bisnis US 2.4).
         */
        startAlarmLoop() {
            this.beep(3, 250); // bunyi pertama: 3 beep
            this.alarmLoop = setInterval(() => {
                if (this.isFinished) {
                    clearInterval(this.alarmLoop);
                    return;
                }
                this.beep(2, 300);
            }, 10000); // ulangi tiap 10 detik
        },
    
        start() {
            if (this.isFinished) return;
    
            // Jika halaman dibuka saat sudah dalam fase warning (reload/back)
            if (this.totalSeconds <= 60 && this.totalSeconds > 0) {
                this.showWarningPopup = true;
                this.startAlarmLoop();
            }
    
            this.interval = setInterval(() => {
                if (this.totalSeconds > 0) {
                    this.totalSeconds--;
    
                    // Tepat saat menyentuh 60 detik → aktifkan alarm
                    if (this.totalSeconds === 60) {
                        this.showWarningPopup = true;
                        this.startAlarmLoop();
                    }
    
                    // Bunyi ekstra tiap 10 detik terakhir (10, 20, 30...)
                    // sudah ditangani alarmLoop di atas
                } else {
                    this.isFinished = true;
                    this.showWarningPopup = false; // tutup popup saat selesai
                    clearInterval(this.interval);
                    clearInterval(this.alarmLoop);
                    this.beep(5, 200); // bunyi panjang saat selesai
                }
            }, 1000);
        }
    }" x-init="start()">
        <div class="w-full max-w-md relative">

            {{-- ============================================================
             POPUP PERINGATAN MERAH (US 2.4)
             Muncul saat ≤ 60 detik tersisa.
             Tidak bisa ditutup manual — hilang otomatis saat timer 00:00
             ============================================================ --}}
            <div x-show="showWarningPopup" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-4 rounded-xl border-2 border-red-500 bg-red-50 shadow-lg overflow-hidden"
                style="display: none;">
                {{-- Header popup --}}
                <div class="bg-red-500 px-5 py-3 flex items-center gap-3">
                    <span class="text-2xl animate-bounce">🚨</span>
                    <div>
                        <p class="text-white font-bold text-sm leading-tight">
                            PERINGATAN — Segera Angkat Sampel!
                        </p>
                        <p class="text-red-100 text-xs">
                            Waktu pemanasan hampir habis
                        </p>
                    </div>
                    {{-- Tidak ada tombol tutup — sesuai aturan bisnis US 2.4 --}}
                </div>

                {{-- Body popup --}}
                <div class="px-5 py-4 space-y-2">
                    <p class="text-red-700 text-sm font-semibold">
                        Sampel:
                        <span class="font-bold text-red-900">
                            {{ $thermalLog->sample->sample_code }} —
                            {{ $thermalLog->sample->patient_name }}
                        </span>
                    </p>
                    <p class="text-red-600 text-sm">
                        Sisa waktu:
                        <span class="font-mono font-extrabold text-red-700 animate-pulse"
                            x-text="minutes + ':' + seconds"></span>
                    </p>
                    <div
                        class="mt-2 bg-red-100 border border-red-200 rounded-lg p-3 text-xs text-red-700 leading-relaxed">
                        ⚠️ <strong>Perhatian:</strong> Paparan panas berlebih dapat mendegradasi RNA
                        dan menyebabkan hasil <em>false negative</em>.
                        Angkat sampel segera setelah timer mencapai <strong>00:00</strong>.
                    </div>
                    <p class="text-xs text-red-400 text-center mt-2">
                        Peringatan ini tidak dapat ditutup secara manual.
                    </p>
                </div>
            </div>

            {{-- Info Sampel --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Sampel Aktif</p>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $thermalLog->sample->sample_code }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ $thermalLog->sample->patient_name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Suhu</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ number_format($thermalLog->temperature_celsius, 1) }}°C
                        </p>
                    </div>
                </div>
                <div class="text-xs text-gray-400 flex justify-between">
                    <span>Mulai: {{ $thermalLog->started_at->format('H:i:s') }}</span>
                    <span>Selesai: {{ $endsAt->format('H:i:s') }}</span>
                </div>
            </div>

            {{-- Timer Countdown --}}
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">

                {{-- Label Status --}}
                <p class="text-xs font-semibold uppercase tracking-widest mb-4"
                    :class="isFinished
                        ?
                        'text-green-500' :
                        isWarning ? 'text-red-500' : 'text-blue-500'">
                    <span x-show="!isFinished && !isWarning">⏳ Sedang Dipanaskan</span>
                    <span x-show="isWarning && !isFinished">🔥 Segera Angkat Sampel!</span>
                    <span x-show="isFinished">✅ Pemanasan Selesai</span>
                </p>

                {{-- Angka Countdown --}}
                <p class="text-8xl font-mono font-extrabold tabular-nums mb-6 transition-colors duration-500"
                    :class="isFinished
                        ?
                        'text-green-500' :
                        isWarning ? 'text-red-500 animate-pulse' : 'text-gray-800'"
                    x-text="isFinished ? '00:00' : (minutes + ':' + seconds)"></p>

                {{-- Progress Bar --}}
                <div class="w-full bg-gray-100 rounded-full h-3 mb-6 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-1000"
                        :class="isFinished
                            ?
                            'bg-green-400' :
                            isWarning ? 'bg-red-400' : 'bg-blue-400'"
                        :style="'width: ' + progress + '%'"></div>
                </div>

                {{-- Info Durasi --}}
                <p class="text-xs text-gray-400 mb-6">
                    Total durasi: <strong>{{ $thermalLog->duration_minutes }} menit</strong>
                </p>

                {{-- Tombol Selesai — disabled sampai 00:00, akan dihubungkan di US 2.5 --}}
                {{-- Tombol Selesai Dipanaskan (US 2.5)
                - Selama timer berjalan: tampil sebagai button disabled (tidak bisa diklik)
                - Saat timer 00:00: berubah menjadi form POST ke thermal.complete
           --}}
                <div>
                    {{-- Placeholder disabled saat timer masih berjalan --}}
                    <div x-show="!isFinished">
                        <button disabled
                            class="w-full px-4 py-3 bg-gray-200 text-gray-400 font-semibold rounded-lg
                              cursor-not-allowed text-sm">
                            Selesai Dipanaskan (aktif saat 00:00)
                        </button>
                    </div>

                    {{-- Form POST aktif saat timer 00:00 --}}
                    <div x-show="isFinished" style="display:none;">
                        <form action="{{ route('thermal.complete', $thermalLog->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white
                                  font-semibold rounded-lg transition duration-200 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                ✅ Selesai Dipanaskan — Pindahkan ke Tahap PCR
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Link kembali --}}
            <div class="mt-4 text-center">
                <a href="{{ route('thermal.queue') }}" class="text-sm text-blue-500 hover:underline">
                    ← Kembali ke Dasbor Antrean
                </a>
            </div>

        </div>
    </div>
</body>

</html>
