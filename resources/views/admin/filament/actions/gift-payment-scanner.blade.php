<div
    x-data="{
        scanner: null,
        scriptLoadingPromise: null,
        readerId: 'gift-payment-qr-reader-' + Math.random().toString(36).slice(2),
        statusMessage: @js(__('messages.filament.gift_payment.scan_hint')),
        statusError: false,
        modalRoot() {
            return this.$root.closest('[role=\'dialog\']') || document
        },
        tokenInput() {
            return this.modalRoot().querySelector('[data-gift-payment-token]')
        },
        setStatus(message, isError = false) {
            this.statusMessage = message
            this.statusError = isError
        },
        setToken(token) {
            const input = this.tokenInput()
            if (!input) return
            input.value = token
            input.dispatchEvent(new Event('input', { bubbles: true }))
            input.dispatchEvent(new Event('change', { bubbles: true }))
        },
        async ensureScannerLib() {
            if (window.Html5Qrcode) return
            if (this.scriptLoadingPromise) {
                await this.scriptLoadingPromise
                return
            }

            this.scriptLoadingPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script')
                script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'
                script.onload = resolve
                script.onerror = reject
                document.head.appendChild(script)
            })

            await this.scriptLoadingPromise
        },
        async stopScan() {
            if (this.scanner) {
                try { await this.scanner.stop() } catch (_) {}
                try { await this.scanner.clear() } catch (_) {}
                this.scanner = null
            }
            this.$refs.readerWrap.classList.add('hidden')
        },
        async startScan() {
            try {
                await this.ensureScannerLib()
            } catch (_) {
                this.setStatus(@js(__('messages.filament.gift_payment.scan_load_error')), true)
                return
            }

            await this.stopScan()
            this.$refs.readerWrap.classList.remove('hidden')

            this.scanner = new Html5Qrcode(this.readerId)
            this.setStatus(@js(__('messages.filament.gift_payment.scan_starting')))

            const config = { fps: 10, qrbox: { width: 240, height: 240 } }
            const onSuccess = async (decodedText) => {
                this.setToken((decodedText || '').trim())
                this.setStatus(@js(__('messages.filament.gift_payment.scan_success')))
                await this.stopScan()
            }
            const onError = () => {}

            try {
                await this.scanner.start({ facingMode: { exact: 'environment' } }, config, onSuccess, onError)
            } catch (_) {
                try {
                    await this.scanner.start({ facingMode: 'environment' }, config, onSuccess, onError)
                } catch (_) {
                    await this.stopScan()
                    this.setStatus(@js(__('messages.filament.gift_payment.scan_camera_error')), true)
                }
            }
        },
    }"
    class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-white/10 dark:bg-gray-800/40"
>
    <div class="flex flex-wrap items-center gap-2">
        <x-filament::button type="button" size="sm" color="warning" x-on:click="startScan">
            {{ __('messages.filament.gift_payment.scan_button') }}
        </x-filament::button>
    </div>

    <p class="mt-2 text-xs" :class="statusError ? 'text-rose-600 dark:text-rose-400' : 'text-gray-600 dark:text-gray-300'">
        <span x-text="statusMessage"></span>
    </p>

    <div x-ref="readerWrap" class="mt-2 hidden overflow-hidden rounded-md border border-gray-300 bg-black p-2 dark:border-white/10">
        <div :id="readerId" class="mx-auto max-w-sm"></div>
    </div>
</div>
