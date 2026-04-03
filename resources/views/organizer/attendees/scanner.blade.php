<x-organizer-layout>
    @section('page-title', 'QR Scanner — ' . $event->title)
    @section('page-subtitle', 'Scan attendee tickets at the gate')

    @section('header-actions')
        <a href="{{ route('organizer.events.attendees', $event) }}" class="btn-secondary text-sm">
            View Attendee List
        </a>
    @endsection

    <div class="max-w-xl mx-auto" x-data="qrScanner('{{ route('organizer.check-in') }}', '{{ csrf_token() }}')">

        {{-- Result banner --}}
        <div x-show="result" x-transition class="mb-4 rounded-xl p-4 text-sm font-medium flex items-start gap-3"
             :class="success ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-700'">
            <svg x-show="success" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="!success" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p x-text="message"></p>
                <template x-if="ticket">
                    <div class="mt-2 text-xs font-normal space-y-0.5">
                        <p><span class="font-semibold">Name:</span> <span x-text="ticket.holder_name"></span></p>
                        <p><span class="font-semibold">Ticket:</span> <span x-text="ticket.ticket_type"></span></p>
                        <p><span class="font-semibold">Code:</span> <span class="font-mono" x-text="ticket.ticket_code"></span></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Camera scanner --}}
        <div class="card p-5 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-900 text-sm">Camera Scanner</h2>
                <button @click="toggleCamera()" class="btn-secondary text-xs py-1.5 px-3" x-text="cameraActive ? 'Stop Camera' : 'Start Camera'"></button>
            </div>

            <div class="relative bg-gray-900 rounded-xl overflow-hidden" style="aspect-ratio:4/3;" x-show="cameraActive">
                <video id="scanner-video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                {{-- Scan crosshair overlay --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-52 h-52 relative">
                        <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white rounded-tl-lg"></span>
                        <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white rounded-tr-lg"></span>
                        <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white rounded-bl-lg"></span>
                        <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white rounded-br-lg"></span>
                        <div x-show="scanning" class="absolute inset-x-0 top-0 h-0.5 bg-primary-400 animate-scan-line"></div>
                    </div>
                </div>
                <canvas id="scanner-canvas" class="hidden"></canvas>
            </div>

            <div x-show="!cameraActive" class="rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center py-12 text-gray-400 text-sm gap-2">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Click <strong>Start Camera</strong> to begin scanning
            </div>
        </div>

        {{-- Manual entry --}}
        <div class="card p-5">
            <h2 class="font-semibold text-gray-900 text-sm mb-4">Manual Ticket Code</h2>
            <form @submit.prevent="manualCheckIn()" class="flex gap-2">
                <input type="text" x-model="manualCode" placeholder="TK-XXXXXXXXXX"
                       class="input flex-1 font-mono uppercase text-sm" maxlength="13" autocomplete="off">
                <button type="submit" class="btn-primary text-sm px-5" :disabled="loading">
                    <span x-show="!loading">Check In</span>
                    <span x-show="loading">…</span>
                </button>
            </form>
        </div>

        {{-- Live counter --}}
        <div class="mt-5 grid grid-cols-2 gap-4">
            <div class="card p-4 text-center">
                <p class="text-3xl font-bold text-green-600" x-text="sessionCheckins">0</p>
                <p class="text-xs text-gray-500 mt-1">Checked in this session</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-3xl font-bold text-red-500" x-text="sessionFailed">0</p>
                <p class="text-xs text-gray-500 mt-1">Scan errors this session</p>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    function qrScanner(checkInUrl, csrfToken) {
        return {
            cameraActive: false,
            scanning: false,
            loading: false,
            manualCode: '',
            result: null,
            success: false,
            message: '',
            ticket: null,
            sessionCheckins: 0,
            sessionFailed: 0,
            videoStream: null,
            scanInterval: null,

            async toggleCamera() {
                if (this.cameraActive) {
                    this.stopCamera();
                } else {
                    await this.startCamera();
                }
            },

            async startCamera() {
                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' }
                    });
                    this.$nextTick(() => {
                        const video = document.getElementById('scanner-video');
                        video.srcObject = this.videoStream;
                        this.cameraActive = true;
                        this.scanning = true;
                        this.startScanning();
                    });
                } catch (e) {
                    this.showResult(false, 'Camera access denied. Use manual entry below.', null);
                }
            },

            stopCamera() {
                clearInterval(this.scanInterval);
                this.videoStream?.getTracks().forEach(t => t.stop());
                this.cameraActive = false;
                this.scanning = false;
            },

            startScanning() {
                // Use BarcodeDetector API if available, fallback to canvas polling
                if (typeof BarcodeDetector !== 'undefined') {
                    const detector = new BarcodeDetector({ formats: ['qr_code'] });
                    const video = document.getElementById('scanner-video');
                    this.scanInterval = setInterval(async () => {
                        try {
                            const barcodes = await detector.detect(video);
                            if (barcodes.length > 0) {
                                const code = barcodes[0].rawValue;
                                if (code && !this.loading) {
                                    await this.doCheckIn(code);
                                }
                            }
                        } catch (_) {}
                    }, 600);
                }
                // If BarcodeDetector not supported, user must use manual entry
            },

            async manualCheckIn() {
                const code = this.manualCode.trim().toUpperCase();
                if (!code) return;
                await this.doCheckIn(code);
                this.manualCode = '';
            },

            async doCheckIn(code) {
                if (this.loading) return;
                this.loading = true;
                try {
                    const res = await fetch(checkInUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ticket_code: code }),
                    });
                    const data = await res.json();
                    this.showResult(data.success, data.message, data.ticket ?? null);
                    if (data.success) this.sessionCheckins++;
                    else this.sessionFailed++;
                } catch (e) {
                    this.showResult(false, 'Network error. Please try again.', null);
                    this.sessionFailed++;
                } finally {
                    this.loading = false;
                }
            },

            showResult(isSuccess, msg, ticketData) {
                this.success = isSuccess;
                this.message = msg;
                this.ticket = ticketData;
                this.result = true;
                clearTimeout(this._resultTimer);
                this._resultTimer = setTimeout(() => { this.result = false; }, 6000);
            },
        };
    }
    </script>
    @endpush

</x-organizer-layout>
