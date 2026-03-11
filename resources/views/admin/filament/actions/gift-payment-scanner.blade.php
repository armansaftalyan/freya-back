<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-white/10 dark:bg-gray-800/40">
    <div class="flex flex-wrap items-center gap-2">
        <x-filament::button type="button" size="sm" color="warning" onclick="window.startGiftPaymentScan(this)">
            {{ __('messages.filament.gift_payment.scan_button') }}
        </x-filament::button>
    </div>

    <p class="mt-2 text-xs text-gray-600 dark:text-gray-300" data-gift-payment-scan-status>
        {{ __('messages.filament.gift_payment.scan_hint') }}
    </p>

    <div class="mt-2 hidden overflow-hidden rounded-md border border-gray-300 bg-black p-2 dark:border-white/10" data-gift-payment-reader-wrap>
        <div id="gift-payment-qr-reader" class="mx-auto max-w-sm"></div>
    </div>
</div>

<script>
(() => {
    if (window.__giftPaymentScannerInitialized) return
    window.__giftPaymentScannerInitialized = true

    let scannerInstance = null
    let scriptLoadingPromise = null

    function getModalRoot(fromEl) {
        return fromEl?.closest('[role="dialog"]') || document
    }

    function findTokenInput(root) {
        return root.querySelector('input[wire\\:model*="mountedActionsData"][wire\\:model*="token"]')
    }

    function getStatusNode(root) {
        return root.querySelector('[data-gift-payment-scan-status]')
    }

    function getReaderWrap(root) {
        return root.querySelector('[data-gift-payment-reader-wrap]')
    }

    function setStatus(root, message, isError = false) {
        const node = getStatusNode(root)
        if (!node) return
        node.textContent = message
        node.classList.toggle('text-rose-600', isError)
        node.classList.toggle('text-gray-600', !isError)
        node.classList.toggle('dark:text-rose-400', isError)
        node.classList.toggle('dark:text-gray-300', !isError)
    }

    function setToken(root, token) {
        const input = findTokenInput(root)
        if (!input) return
        input.value = token
        input.dispatchEvent(new Event('input', { bubbles: true }))
        input.dispatchEvent(new Event('change', { bubbles: true }))
    }

    async function ensureLib() {
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

    async function stopInternal(root) {
        if (scannerInstance) {
            try { await scannerInstance.stop() } catch (_) {}
            try { await scannerInstance.clear() } catch (_) {}
            scannerInstance = null
        }

        getReaderWrap(root)?.classList.add('hidden')
    }

    window.stopGiftPaymentScan = async (buttonEl) => {
        const root = getModalRoot(buttonEl)
        await stopInternal(root)
        setStatus(root, @json(__('messages.filament.gift_payment.scan_stopped')))
    }

    window.startGiftPaymentScan = async (buttonEl) => {
        const root = getModalRoot(buttonEl)
        const readerWrap = getReaderWrap(root)
        if (!readerWrap) return

        try {
            await ensureLib()
        } catch (_) {
            setStatus(root, @json(__('messages.filament.gift_payment.scan_load_error')), true)
            return
        }

        await stopInternal(root)
        readerWrap.classList.remove('hidden')
        scannerInstance = new Html5Qrcode('gift-payment-qr-reader')
        setStatus(root, @json(__('messages.filament.gift_payment.scan_starting')))

        const onScanSuccess = async (decodedText) => {
            setToken(root, (decodedText || '').trim())
            setStatus(root, @json(__('messages.filament.gift_payment.scan_success')))
            await stopInternal(root)
        }

        const onScanFailure = () => {}
        const config = { fps: 10, qrbox: { width: 240, height: 240 } }

        try {
            await scannerInstance.start({ facingMode: { exact: 'environment' } }, config, onScanSuccess, onScanFailure)
        } catch (_) {
            try {
                await scannerInstance.start({ facingMode: 'environment' }, config, onScanSuccess, onScanFailure)
            } catch (_) {
                await stopInternal(root)
                setStatus(root, @json(__('messages.filament.gift_payment.scan_camera_error')), true)
            }
        }
    }
})()
</script>
