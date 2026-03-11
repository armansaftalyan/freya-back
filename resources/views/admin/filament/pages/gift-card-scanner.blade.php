<x-filament-panels::page>
    <div class="max-w-4xl space-y-6" data-gift-card-scanner>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.filament.gift_card_scanner.title') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('messages.filament.gift_card_scanner.scan_hint') }}</p>
            </div>

            <div class="space-y-4">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('messages.filament.gift_card_scanner.token') }}
                    </span>
                    <input
                        type="text"
                        wire:model.defer="data.token"
                        placeholder="{{ __('messages.filament.gift_card_scanner.token_placeholder') }}"
                        data-token-input
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 dark:border-white/10 dark:bg-gray-950 dark:text-white"
                    >
                </label>

                <div class="flex flex-wrap gap-2">
                    <x-filament::button type="button" color="warning" size="sm" data-scan-start onclick="window.startGiftCardScan()">
                        {{ __('messages.filament.gift_card_scanner.scan_button') }}
                    </x-filament::button>
                    <x-filament::button type="button" color="gray" size="sm" data-scan-stop onclick="window.stopGiftCardScan()">
                        {{ __('messages.filament.gift_card_scanner.stop_scan_button') }}
                    </x-filament::button>
                    <x-filament::button wire:click="findCard" color="primary" size="sm">
                        {{ __('messages.filament.gift_card_scanner.find_button') }}
                    </x-filament::button>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:border-white/10 dark:bg-gray-800/60 dark:text-gray-300" data-scan-status>
                    {{ __('messages.filament.gift_card_scanner.scan_hint') }}
                </div>

                <div class="hidden overflow-hidden rounded-lg border border-gray-300 bg-black p-2 dark:border-white/10" data-qr-reader-wrap>
                    <div id="gift-card-qr-reader" data-qr-reader class="mx-auto max-w-md"></div>
                </div>
            </div>
        </div>

        @if ($this->giftCard)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ __('messages.filament.gift_card_scanner.card_details') }}
                </h3>

                <dl class="mt-4 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">ID</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $this->giftCard->id }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.code') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $this->giftCard->code }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.status') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ __('messages.filament.gift_card_status.' . $this->giftCard->status->value) }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.balance') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ number_format((float) $this->giftCard->balance, 0, '.', ' ') }} {{ $this->giftCard->currency }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.initial_amount') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ number_format((float) $this->giftCard->initial_amount, 0, '.', ' ') }} {{ $this->giftCard->currency }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.expires_at') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $this->giftCard->expires_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.owner') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $this->giftCard->owner?->name ?? '—' }}</dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.filament.fields.order') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $this->giftCard->order?->id ? '#'.$this->giftCard->order->id : '—' }}</dd>
                    </div>
                </dl>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-5 text-sm text-gray-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-400">
                {{ __('messages.filament.gift_card_scanner.empty_state') }}
            </div>
        @endif
    </div>
</x-filament-panels::page>

@once
    @push('scripts')
        <script>
            (() => {
                let scannerInstance = null
                let scriptLoadingPromise = null

                function getScannerElements() {
                    const page = document.querySelector('[data-gift-card-scanner]')
                    if (!page) return null

                    return {
                        tokenInput: page.querySelector('[data-token-input]'),
                        status: page.querySelector('[data-scan-status]'),
                        reader: page.querySelector('[data-qr-reader]'),
                        readerWrap: page.querySelector('[data-qr-reader-wrap]'),
                    }
                }

                function setStatus(message, isError = false) {
                    const els = getScannerElements()
                    if (!els?.status) return
                    els.status.textContent = message
                    els.status.classList.toggle('text-rose-600', isError)
                    els.status.classList.toggle('text-gray-600', !isError)
                }

                function setTokenValue(token) {
                    const els = getScannerElements()
                    if (!els?.tokenInput) return
                    els.tokenInput.value = token
                    els.tokenInput.dispatchEvent(new Event('input', { bubbles: true }))
                    els.tokenInput.dispatchEvent(new Event('change', { bubbles: true }))
                }

                async function ensureScannerLibrary() {
                    if (window.Html5Qrcode) return
                    if (scriptLoadingPromise) {
                        await scriptLoadingPromise
                        return
                    }

                    scriptLoadingPromise = new Promise((resolve, reject) => {
                        const script = document.createElement('script')
                        script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'
                        script.onload = resolve
                        script.onerror = reject
                        document.head.appendChild(script)
                    })

                    await scriptLoadingPromise
                }

                async function stopScanInternal() {
                    const els = getScannerElements()
                    if (!els) return

                    if (scannerInstance) {
                        try {
                            await scannerInstance.stop()
                        } catch (_) {}

                        try {
                            await scannerInstance.clear()
                        } catch (_) {}

                        scannerInstance = null
                    }

                    els.readerWrap?.classList.add('hidden')
                }

                window.stopGiftCardScan = async () => {
                    await stopScanInternal()
                    setStatus(@json(__('messages.filament.gift_card_scanner.scan_stopped')))
                }

                window.startGiftCardScan = async () => {
                    const els = getScannerElements()
                    if (!els?.reader || !els.readerWrap) return

                    try {
                        await ensureScannerLibrary()
                    } catch (_) {
                        setStatus(@json(__('messages.filament.gift_card_scanner.scan_load_error')), true)
                        return
                    }

                    await stopScanInternal()
                    els.readerWrap.classList.remove('hidden')

                    scannerInstance = new Html5Qrcode('gift-card-qr-reader')
                    setStatus(@json(__('messages.filament.gift_card_scanner.scan_starting')))

                    const onScanSuccess = async (decodedText) => {
                        setTokenValue(decodedText.trim())
                        setStatus(@json(__('messages.filament.gift_card_scanner.scan_success')))
                        await stopScanInternal()
                    }

                    const onScanFailure = () => {}

                    const scanConfig = {
                        fps: 10,
                        qrbox: { width: 260, height: 260 },
                    }

                    try {
                        await scannerInstance.start(
                            { facingMode: { exact: 'environment' } },
                            scanConfig,
                            onScanSuccess,
                            onScanFailure,
                        )
                    } catch (_) {
                        try {
                            await scannerInstance.start(
                                { facingMode: 'environment' },
                                scanConfig,
                                onScanSuccess,
                                onScanFailure,
                            )
                        } catch (error) {
                            await stopScanInternal()
                            setStatus(@json(__('messages.filament.gift_card_scanner.scan_camera_error')), true)
                        }
                    }
                }
            })()
        </script>
    @endpush
@endonce
