<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Http\Controllers\Controller;
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

        if ($amount !== number_format((float) $order->amount, 2, '.', '')) {
            Log::warning('Idram callback rejected: amount mismatch', ['order_id' => $orderId, 'amount' => $amount]);
            return response('ERROR', 400, ['Content-Type' => 'text/plain; charset=utf-8']);
        }

        if ($request->input('EDP_PRECHECK') === 'YES') {
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

        return response('OK', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
