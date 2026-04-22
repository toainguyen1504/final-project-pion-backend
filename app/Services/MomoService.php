<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoService
{
    public function createPayment(Order $order): array
    {
        $enabled = (bool) config('services.momo.enabled');

        if (! $enabled) {
            return $this->mockCreatePayment($order);
        }

        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');
        $baseUrl = rtrim(config('services.momo.base_url'), '/');
        $redirectUrl = config('services.momo.redirect_url');
        $ipnUrl = config('services.momo.ipn_url');
        $requestType = config('services.momo.request_type', 'captureWallet');

        if (! $partnerCode || ! $accessKey || ! $secretKey) {
            return $this->mockCreatePayment($order);
        }

        $requestId = (string) Str::uuid();
        $orderId = $order->order_number;
        $amount = (string) (int) $order->final_amount;
        $orderInfo = 'Thanh toan khoa hoc PRO - ' . $order->order_number;
        $extraData = base64_encode(json_encode([
            'order_number' => $order->order_number,
            'payer_id' => $order->payer_id,
        ], JSON_UNESCAPED_UNICODE));

        $rawSignature = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$ipnUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}"
            . "&redirectUrl={$redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'PION Education',
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
            ->post($baseUrl . '/v2/gateway/api/create', $payload)
            ->json();

        return [
            'is_mock' => false,
            'request_id' => $requestId,
            'payload' => $payload,
            'response' => $response,
            'result_code' => $response['resultCode'] ?? 99,
            'message' => $response['message'] ?? 'Không thể tạo thanh toán MoMo.',
            'pay_url' => $response['payUrl'] ?? null,
            'deeplink' => $response['deeplink'] ?? null,
            'qr_code_url' => $response['qrCodeUrl'] ?? null,
            'momo_order_id' => $orderId,
            'momo_request_id' => $requestId,
        ];
    }

    public function mockCreatePayment(Order $order): array
    {
        $requestId = 'MOCK-' . Str::upper(Str::random(10));

        return [
            'is_mock' => true,
            'request_id' => $requestId,
            'payload' => [
                'mock' => true,
                'order_number' => $order->order_number,
                'amount' => $order->final_amount,
            ],
            'response' => [
                'resultCode' => 0,
                'message' => 'Mock payment session created.',
            ],
            'result_code' => 0,
            'message' => 'Mock payment session created.',
            'pay_url' => url('/mock-payment/' . $order->order_number),
            'deeplink' => null,
            'qr_code_url' => 'mock-payment:' . $order->order_number,
            'momo_order_id' => 'MOCK-' . $order->order_number,
            'momo_request_id' => $requestId,
        ];
    }

    public function verifyIpnSignature(array $data): bool
    {
        $secretKey = config('services.momo.secret_key');

        if (! $secretKey || empty($data['signature'])) {
            return false;
        }

        $receivedSignature = $data['signature'];
        unset($data['signature']);

        ksort($data);

        $rawSignature = collect($data)
            ->map(fn($value, $key) => $key . '=' . $value)
            ->implode('&');

        $expectedSignature = hash_hmac('sha256', $rawSignature, $secretKey);

        return hash_equals($expectedSignature, $receivedSignature);
    }
}
