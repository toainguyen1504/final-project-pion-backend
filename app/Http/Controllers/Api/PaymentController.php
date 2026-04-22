<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createMomoPayment(Request $request, MomoService $momoService)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $user = $request->user();
        $learnerId = $this->resolveLearnerId($user);

        if (! $learnerId) {
            return response()->json([
                'message' => 'Không tìm thấy learner tương ứng với tài khoản hiện tại.',
            ], 422);
        }

        $course = Course::findOrFail($validated['course_id']);

        if ($course->is_free) {
            return response()->json([
                'message' => 'Khóa học này là miễn phí, không cần thanh toán.',
            ], 422);
        }

        $alreadyPaid = Enrollment::where('learner_id', $learnerId)
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return response()->json([
                'message' => 'Bạn đã sở hữu khóa học này rồi.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $price = (float) ($course->discount_price ?: $course->price);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'payer_id' => $user->id,
                'status' => Order::STATUS_PENDING,
                'payment_method' => 'momo',
                'payment_status' => 'pending',
                'total_amount' => $course->price,
                'discount_amount' => max(0, (float) $course->price - $price),
                'final_amount' => $price,
                'ordered_at' => now(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'amount' => $price,
                'tax' => 0,
                'discount' => max(0, (float) $course->price - $price),
                'paid_at' => null,
            ]);

            $payment = $momoService->createPayment($order);

            $order->update([
                'momo_order_id' => $payment['momo_order_id'] ?? null,
                'momo_request_id' => $payment['momo_request_id'] ?? null,
                'payment_payload' => $payment['payload'] ?? null,
                'payment_result' => $payment['response'] ?? null,
            ]);

            if (($payment['result_code'] ?? 99) !== 0) {
                $order->update([
                    'status' => Order::STATUS_FAILED,
                    'payment_status' => 'failed',
                ]);

                DB::commit();

                return response()->json([
                    'message' => $payment['message'] ?? 'Không thể tạo thanh toán.',
                    'data' => [
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                    ],
                ], 422);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tạo phiên thanh toán thành công.',
                'data' => [
                    'order_number' => $order->order_number,
                    'amount' => $order->final_amount,
                    'status' => $order->status,
                    'is_mock' => $payment['is_mock'] ?? false,
                    'pay_url' => $payment['pay_url'] ?? null,
                    'deeplink' => $payment['deeplink'] ?? null,
                    'qr_code_url' => $payment['qr_code_url'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Không thể tạo thanh toán.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrderStatus(Request $request, string $orderNumber)
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)
            ->where('payer_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'amount' => $order->final_amount,
                'paid_at' => optional($order->paid_at)?->toDateTimeString(),
            ],
        ]);
    }

    public function mockSuccess(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'exists:orders,order_number'],
        ]);

        $user = $request->user();
        $learnerId = $this->resolveLearnerId($user);

        if (! $learnerId) {
            return response()->json([
                'message' => 'Không tìm thấy learner tương ứng với tài khoản hiện tại.',
            ], 422);
        }

        $order = Order::where('order_number', $validated['order_number'])
            ->where('payer_id', $user->id)
            ->firstOrFail();

        if ($order->status === Order::STATUS_PAID) {
            return response()->json([
                'message' => 'Đơn hàng này đã thanh toán rồi.',
            ]);
        }

        $item = $order->items()->first();

        if (! $item) {
            return response()->json([
                'message' => 'Đơn hàng không có sản phẩm.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order->update([
                'status' => Order::STATUS_PAID,
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_result' => array_merge($order->payment_result ?? [], [
                    'mock_callback' => true,
                    'resultCode' => 0,
                    'message' => 'Mock payment success',
                ]),
            ]);

            if ($item) {
                $item->update([
                    'paid_at' => now(),
                ]);
            }

            Enrollment::updateOrCreate(
                [
                    'learner_id' => $learnerId,
                    'course_id' => $item->course_id,
                ],
                [
                    'payment_status' => 'paid',
                    'payment_source' => 'momo',
                    'enrollment_date' => now(),
                    'progress' => 0,
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Thanh toán thành công.',
                'data' => [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Không thể cập nhật thanh toán.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function resolveLearnerId($user): ?int
    {
        if (method_exists($user, 'learner') && $user->learner) {
            return $user->learner->id;
        }

        return null;
    }

    protected function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
    }
}
