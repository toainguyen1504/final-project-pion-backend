<?php
// app/Services/MomoPaymentService.php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoPaymentService
{
    public function createPayment(Order $order): array
    {
        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');
        $redirectUrl = config('services.momo.redirect_url');
        $ipnUrl = config('services.momo.ipn_url');
        $requestType = config('services.momo.request_type', 'captureWallet');

        $requestId = (string) Str::uuid();
        $orderId = $order->order_number;
        $amount = (int) round($order->final_amount);
        $orderInfo = 'Thanh toan khoa hoc PION #' . $order->order_number;
        $extraData = base64_encode(json_encode([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]));

        $rawSignature =
            "accessKey={$accessKey}" .
            "&amount={$amount}" .
            "&extraData={$extraData}" .
            "&ipnUrl={$ipnUrl}" .
            "&orderId={$orderId}" .
            "&orderInfo={$orderInfo}" .
            "&partnerCode={$partnerCode}" .
            "&redirectUrl={$redirectUrl}" .
            "&requestId={$requestId}" .
            "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'PION',
            'storeId' => 'PION',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'requestType' => $requestType,
            'autoCapture' => true,
            'extraData' => $extraData,
            'signature' => $signature,
        ];

        $response = Http::timeout(30)
            ->acceptJson()
            ->post(config('services.momo.base_url') . '/v2/gateway/api/create', $payload)
            ->throw()
            ->json();

        $order->update([
            'momo_order_id' => $orderId,
            'momo_request_id' => $requestId,
            'payment_method' => 'momo',
            'payment_status' => Order::STATUS_PENDING,
            'payment_payload' => $payload,
            'payment_result' => $response,
            'expired_at' => now()->addMinutes(15),
        ]);

        return $response;
    }

    public function verifyIpnSignature(array $data): bool
    {
        $secretKey = config('services.momo.secret_key');

        $rawSignature =
            "accessKey=" . config('services.momo.access_key') .
            "&amount=" . ($data['amount'] ?? '') .
            "&extraData=" . ($data['extraData'] ?? '') .
            "&message=" . ($data['message'] ?? '') .
            "&orderId=" . ($data['orderId'] ?? '') .
            "&orderInfo=" . ($data['orderInfo'] ?? '') .
            "&orderType=" . ($data['orderType'] ?? '') .
            "&partnerCode=" . ($data['partnerCode'] ?? '') .
            "&payType=" . ($data['payType'] ?? '') .
            "&requestId=" . ($data['requestId'] ?? '') .
            "&responseTime=" . ($data['responseTime'] ?? '') .
            "&resultCode=" . ($data['resultCode'] ?? '') .
            "&transId=" . ($data['transId'] ?? '');

        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        return hash_equals($signature, $data['signature'] ?? '');
    }
}
