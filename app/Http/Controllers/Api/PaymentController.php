<?php
// app/Http/Controllers/Api/PaymentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Enrollment;
use App\Services\MomoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createMomoPayment(Request $request, MomoPaymentService $momoService)
    {
        $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $user = $request->user();
        $user->loadMissing('learner');

        if (!$user->learner) {
            return response()->json([
                'message' => 'Tài khoản hiện tại không phải học viên.',
            ], 422);
        }

        $course = Course::findOrFail($request->course_id);

        $alreadyPaid = Enrollment::where('learner_id', $user->learner->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return response()->json([
                'message' => 'Bạn đã sở hữu khóa học này.',
            ], 422);
        }

        $price = (float) ($course->price ?? $course->sale_price ?? 0);
        if ($price <= 0) {
            return response()->json([
                'message' => 'Khóa học này không hợp lệ để thanh toán.',
            ], 422);
        }

        $order = DB::transaction(function () use ($user, $course, $price) {
            $order = Order::create([
                'order_number' => 'PION-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'momo',
                'payment_status' => Order::STATUS_PENDING,
                'total_amount' => $price,
                'discount_amount' => 0,
                'final_amount' => $price,
                'ordered_at' => now(),
                'payer_id' => $user->id,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $price,
                'quantity' => 1,
                'subtotal' => $price,
            ]);

            return $order->fresh('items');
        });

        $momo = $momoService->createPayment($order);

        return response()->json([
            'message' => 'Tạo phiên thanh toán thành công.',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->final_amount,
                'pay_url' => $momo['payUrl'] ?? null,
                'deeplink' => $momo['deeplink'] ?? null,
                'qr_code_url' => $momo['qrCodeUrl'] ?? null,
                'result_code' => $momo['resultCode'] ?? null,
            ],
        ]);
    }

    public function momoIpn(Request $request, MomoPaymentService $momoService)
    {
        $data = $request->all();

        if (!$momoService->verifyIpnSignature($data)) {
            return response()->noContent(204);
        }

        $order = Order::where('order_number', $data['orderId'] ?? null)->first();

        if (!$order) {
            return response()->noContent(204);
        }

        if ((int) round($order->final_amount) !== (int) ($data['amount'] ?? 0)) {
            return response()->noContent(204);
        }

        if ($order->payment_status === Order::STATUS_PAID) {
            return response()->noContent(204);
        }

        DB::transaction(function () use ($order, $data) {
            if ((int) ($data['resultCode'] ?? -1) === 0) {
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'payment_status' => Order::STATUS_PAID,
                    'momo_trans_id' => $data['transId'] ?? null,
                    'paid_at' => now(),
                    'payment_result' => $data,
                ]);

                $orderItem = $order->items()->first();
                $user = $order->payer()->with('learner')->first();

                if ($orderItem && $user?->learner) {
                    Enrollment::updateOrCreate(
                        [
                            'course_id' => $orderItem->course_id,
                            'learner_id' => $user->learner->id,
                        ],
                        [
                            'order_id' => $order->id,
                            'payment_status' => 'paid',
                            'payment_source' => 'momo',
                            'enrollment_date' => now(),
                            'progress' => 0,
                        ]
                    );
                }
            } else {
                $order->update([
                    'status' => Order::STATUS_FAILED,
                    'payment_status' => Order::STATUS_FAILED,
                    'payment_result' => $data,
                ]);
            }
        });

        return response()->noContent(204);
    }

    public function getOrderStatus(Request $request, $orderNumber)
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)
            ->where('payer_id', $user->id)
            ->with('items.course')
            ->firstOrFail();

        return response()->json([
            'data' => [
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'amount' => $order->final_amount,
                'paid_at' => $order->paid_at,
                'expired_at' => $order->expired_at,
                'course' => $order->items->first()?->course,
            ],
        ]);
    }
}
