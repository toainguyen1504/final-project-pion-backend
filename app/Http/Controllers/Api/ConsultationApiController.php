<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConsultationRequest;
use App\Services\TelegramService;
use App\Services\GoogleSheetService;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsultationsExport;
// use Mews\Purifier\Facades\Purifier;

class ConsultationApiController extends Controller
{

    // Hàm trả về danh sách consultations (cho admin)
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search');

        $query = Consultation::with('user:id,email,display_name');

        if ($search) {
            $query->where(function ($q) use ($search) {

                // search guest
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")

                    // search user relation
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('display_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $consultations = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $consultations->items(),
            'meta' => [
                'current_page'  => $consultations->currentPage(),
                'last_page'     => $consultations->lastPage(),
                'per_page'      => $consultations->perPage(),
                'total'         => $consultations->total(),
                'next_page_url' => $consultations->nextPageUrl(),
                'prev_page_url' => $consultations->previousPageUrl(),
            ]
        ]);
    }

    // Hàm export Excel
    public function export()
    {
        $filename = 'consultations_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ConsultationsExport, $filename);
    }


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
            $message = "<b>📩 YÊU CẦU TƯ VẤN MỚI</b>\n\n";

            if (Auth::check()) {
                $message .= "<b>👤 Người dùng:</b> {$consultation->user_name}\n";
                $message .= "<b>📞 SĐT:</b> {$consultation->user_phone}\n";
                $message .= "<b>✉️ Email:</b> {$consultation->user_email}\n";
            } else {
                $message .= "<b>👤 Khách:</b> {$consultation->guest_name}\n";
                $message .= "<b>📞 SĐT:</b> {$consultation->guest_phone}\n";
                $message .= "<b>✉️ Email:</b> {$consultation->guest_email}\n";
            }

            $message .= "\n<b>📝 Nội dung:</b>\n";
            $message .= "<i>{$consultation->request_content}</i>\n\n";

            $message .= "<b>⏰ Thời gian:</b> " . now()->format('d/m/Y H:i');

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

    // Stats hiển thị cho dashboard (admin cms)
    public function stats(Request $request)
    {
        $field = $request->get('field', 'created_at');

        $allowedFields = ['created_at', 'updated_at'];

        if (!in_array($field, $allowedFields, true)) {
            $field = 'created_at';
        }

        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();

        $lastMonthStart = $thisMonthStart->copy()->subMonth();
        $lastMonthEnd = $thisMonthStart->copy()->subSecond();

        $thisMonthCount = Consultation::whereBetween($field, [$thisMonthStart, $thisMonthEnd])->count();
        $lastMonthCount = Consultation::whereBetween($field, [$lastMonthStart, $lastMonthEnd])->count();
        $totalCount = Consultation::count();

        return response()->json([
            'success' => true,
            'data' => [
                'this_month' => $thisMonthCount,
                'last_month' => $lastMonthCount,
                'total' => $totalCount,
            ]
        ]);
    }
}
