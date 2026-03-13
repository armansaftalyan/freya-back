<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class IdramResultController extends Controller
{
    public function __construct(private readonly GiftCardService $giftCardService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $orderId = (int) $request->input('EDP_BILL_NO');
        $account = (string) $request->input('EDP_REC_ACCOUNT', '');
        $amount = (string) $request->input('EDP_AMOUNT', '');
        $expectedAccount = (string) config('services.idram.rec_account', '');
        $payload = $request->all();

        Log::info('Idram RESULT callback received', [
            'order_id' => $orderId,
            'payload' => $payload,
        ]);

        /** @var GiftCardOrder|null $order */
        $order = GiftCardOrder::query()->find($orderId);
        if ($order === null) {
            Log::warning('Idram callback rejected: order not found', ['order_id' => $orderId]);
            return response('ERROR', 400, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        if ($expectedAccount === '' || !hash_equals($expectedAccount, $account)) {
            Log::warning('Idram callback rejected: account mismatch', ['order_id' => $orderId]);
            return response('ERROR', 400, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        $expectedAmount = (string) data_get($order->meta, 'idram.expected_amount', number_format((float) $order->amount, 2, '.', ''));

        if ($amount !== $expectedAmount) {
            Log::warning('Idram callback rejected: amount mismatch', ['order_id' => $orderId, 'amount' => $amount]);
            return response('ERROR', 400, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        if ($request->input('EDP_PRECHECK') === 'YES') {
            Log::info('Idram PRECHECK accepted', [
                'order_id' => $orderId,
                'payload' => $payload,
            ]);
            return response('OK', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        $secretKey = (string) config('services.idram.secret_key', '');
        if ($secretKey === '') {
            Log::error('Idram callback rejected: secret key is not configured');
            return response('ERROR', 500, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        $payerAccount = (string) $request->input('EDP_PAYER_ACCOUNT', '');
        $transactionId = (string) $request->input('EDP_TRANS_ID', '');
        $transactionDate = (string) $request->input('EDP_TRANS_DATE', '');
        $receivedChecksum = strtolower((string) $request->input('EDP_CHECKSUM', ''));

        $calculatedChecksum = strtolower(md5(implode(':', [
            $account,
            $amount,
            $secretKey,
            (string) $order->id,
            $payerAccount,
            $transactionId,
            $transactionDate,
        ])));

        if ($receivedChecksum === '' || !hash_equals($calculatedChecksum, $receivedChecksum)) {
            Log::warning('Idram callback rejected: checksum mismatch', ['order_id' => $orderId]);
            return response('ERROR', 400, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        if ($order->status !== GiftCardOrderStatus::Paid) {
            $order->status = GiftCardOrderStatus::Paid;
            $order->paid_at = now();
            $order->provider_payment_id = $transactionId;
            $order->meta = array_merge(
                (array) ($order->meta ?? []),
                [
                    'idram' => [
                        'payer_account' => $payerAccount,
                        'trans_id' => $transactionId,
                        'trans_date' => $transactionDate,
                        'amount' => $amount,
                    ],
                ],
            );
            $order->save();

            $this->giftCardService->issueFromPaidOrder($order);
        }

        Log::info('Idram payment confirmed', [
            'order_id' => $orderId,
            'payload' => $payload,
        ]);

        return response('OK', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public function success(Request $request): RedirectResponse
    {
        Log::info('Idram SUCCESS redirect received', [
            'payload' => $request->all(),
        ]);

        return $this->redirectToFrontend($request, 'success');
    }

    public function fail(Request $request): RedirectResponse
    {
        Log::info('Idram FAIL redirect received', [
            'payload' => $request->all(),
        ]);

        return $this->redirectToFrontend($request, 'fail');
    }

    private function redirectToFrontend(Request $request, string $status): RedirectResponse
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');
        $query = array_filter([
            'payment_provider' => 'idram',
            'payment_status' => $status,
            'order_id' => $request->input('EDP_BILL_NO') ?: $request->input('order_id'),
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        return redirect()->away($frontendUrl.'/gift-cards/buy?'.http_build_query($query));
    }
}
