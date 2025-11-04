<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultationRequest;
use App\Services\TelegramService;
use App\Services\GoogleSheetService;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
// use Mews\Purifier\Facades\Purifier;

class ConsultationApiController extends Controller
{


    public function store(ConsultationRequest $request, TelegramService $telegram, GoogleSheetService $sheetService)
    {
        $data = $request->validated();
        $data['request_content'] = strip_tags($data['request_content']);

        $consultation = new Consultation();

        if (Auth::check()) {
            $consultation->user_id = Auth::id();
            $consultation->user_name = Auth::user()->name;
            $consultation->user_email = Auth::user()->email;
            $consultation->user_phone = Auth::user()->phone;
        } else {
            $consultation->guest_name = $data['guest_name'];
            $consultation->guest_email = $data['guest_email'];
            $consultation->guest_phone = $data['guest_phone'];
        }

        $consultation->request_content = $data['request_content'];
        $consultation->save(); // Lưu DB trước để chắc chắn dữ liệu không bị mất

        // Send a message Telegram safely
        try {
            $message = "<b>📥 Yêu cầu tư vấn mới</b>\n";

            if (Auth::check()) {
                $message .= "👤 Người dùng: {$consultation->user_name}\n";
                $message .= "📞 SĐT: {$consultation->user_phone}\n";
                $message .= "✉️ Email: {$consultation->user_email}\n";
            } else {
                $message .= "👤 Khách: {$consultation->guest_name}\n";
                $message .= "📞 SĐT: {$consultation->guest_phone}\n";
                $message .= "✉️ Email: {$consultation->guest_email}\n";
            }

            $message .= "📝 Nội dung: {$consultation->request_content}\n";
            $message .= "⏰ Thời gian: " . now()->format('d/m/Y H:i');

            $telegram->sendMessage($message);
        } catch (\Exception $e) {
            // Bỏ qua lỗi Telegram, không throw 500
        }

        // Save data on Google Sheet safely
        $spreadsheetId = env('GOOGLE_SHEET_ID');
        $range = 'Sheet1';

        try {
            if ($spreadsheetId) { // Chỉ chạy nếu ID tồn tại
                $sheetService->ensureHeader($spreadsheetId, $range);

                $sheetService->appendRow($spreadsheetId, $range, [
                    Auth::check() ? $consultation->user_name : $consultation->guest_name,
                    Auth::check() ? $consultation->user_email : $consultation->guest_email,
                    Auth::check() ? $consultation->user_phone : $consultation->guest_phone,
                    $consultation->request_content,
                    now()->format('d/m/Y H:i')
                ]);
            }
        } catch (\Exception $e) {
            // Thay vì trả 500, giữ nguyên trả message 200
            return response()->json([
                'message' => 'Tư vấn đã ghi nhận, nhưng lỗi khi ghi Google Sheet',
                'error' => $e->getMessage()
            ], 200);
        }

        return response()->json(['message' => 'Tư vấn của bạn đã được ghi nhận!'], 201);
    }

    public function myConsultations()
    {
        return Consultation::where('user_id', Auth::id())->get();
    }
}
