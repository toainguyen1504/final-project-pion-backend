<?php

namespace App\Exports;

use App\Models\Consultation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConsultationsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Consultation::orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên khách hàng',
            'Email',
            'Số điện thoại',
            'Nội dung yêu cầu',
            'Ngày gửi',
        ];
    }

    public function map($consultation): array
    {
        return [
            $consultation->id,
            $consultation->user_name ?? $consultation->guest_name,
            $consultation->user_email ?? $consultation->guest_email,
            $consultation->user_phone ?? $consultation->guest_phone,
            $consultation->request_content,
            $consultation->created_at->format('d/m/Y H:i'),
        ];
    }
}
