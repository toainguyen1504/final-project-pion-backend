<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoIpnController extends Controller
{
    public function __invoke(Request $request, MomoService $momoService)
    {
        $payload = $request->all();

        if (! $momoService->verifyIpnSignature($payload)) {
            Log::warning('MoMo IPN invalid signature', $payload);
            return response()->noContent(204);
        }

        $order = Order::where('order_number', $payload['orderId'] ?? null)->first();

        if (! $order) {
            Log::warning('MoMo IPN order not found', $payload);
            return response()->noContent(204);
        }

        if ((float) $order->final_amount !== (float) ($payload['amount'] ?? 0)) {
            Log::warning('MoMo IPN amount mismatch', [
                'order_number' => $order->order_number,
                'payload' => $payload,
            ]);
            return response()->noContent(204);
        }

        if ($order->status === Order::STATUS_PAID) {
            return response()->noContent(204);
        }

        $item = $order->items()->first();

        if (! $item) {
            Log::warning('MoMo IPN order has no items', [
                'order_number' => $order->order_number,
            ]);
            return response()->noContent(204);
        }

        DB::transaction(function () use ($order, $item, $payload) {
            $order->update([
                'momo_trans_id' => $payload['transId'] ?? null,
                'payment_result' => $payload,
            ]);

            if ((int) ($payload['resultCode'] ?? -1) === 0) {
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);

                $learnerId = optional($order->payer)->learner?->id;

                if ($learnerId) {
                    $enrollment = Enrollment::where('learner_id', $learnerId)
                        ->where('course_id', $item->course_id)
                        ->lockForUpdate()
                        ->first();

                    $shouldIncrementParticipants = false;

                    if (! $enrollment) {
                        $enrollment = Enrollment::create([
                            'learner_id' => $learnerId,
                            'course_id' => $item->course_id,
                            'payment_status' => 'paid',
                            'payment_source' => 'momo',
                            'enrollment_date' => now(),
                            'progress' => 0,
                        ]);

                        $shouldIncrementParticipants = true;
                    } else {
                        $wasPaid = $enrollment->payment_status === 'paid';

                        $enrollment->update([
                            'payment_status' => 'paid',
                            'payment_source' => 'momo',
                            'enrollment_date' => $enrollment->enrollment_date ?? now(),
                        ]);

                        if (! $wasPaid) {
                            $shouldIncrementParticipants = true;
                        }
                    }

                    if ($shouldIncrementParticipants) {
                        $item->course()->increment('participants');
                    }
                }
            } else {
                $order->update([
                    'status' => Order::STATUS_FAILED,
                    'payment_status' => 'failed',
                ]);
            }
        });

        return response()->noContent(204);
    }
}
