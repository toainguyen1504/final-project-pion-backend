<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultationRequest;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
// use Mews\Purifier\Facades\Purifier;


class ConsultationApiController extends Controller
{
    public function store(ConsultationRequest $request)
    {
        $data = $request->validated();
        // Filter XSS - Purifier is applied to sanitize request_content, removing <script> tags and other harmful HTML.
        // $data['request_content'] = Purifier::clean($data['request_content'], [
        //     'HTML.Allowed' => 'b,strong,i,em,u,br',
        // ]);
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
        // $consultation->status = 'pending';
        $consultation->save();

        return response()->json(['message' => 'Tư vấn của bạn đã được ghi nhận!'], 201);
    }

    public function myConsultations()
    {
        return Consultation::where('user_id', Auth::id())->get();
    }
}
