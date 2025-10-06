<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleSheetsFormService;

class FormController extends Controller
{
    protected $sheets;

    public function __construct(GoogleSheetsFormService $sheets)
    {
        $this->sheets = $sheets;
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'birth_year' => 'required|numeric',
            'email' => 'required|email',
            'phone' => 'required|string',
            'question' => 'nullable|string',
        ]);

        $this->sheets->appendRow([
            $request->name,
            $request->birth_year,
            $request->email,
            $request->phone,
            $request->question ?? '',
            now()->toDateTimeString(),
        ]);

        return response()->json(['success' => true, 'message' => 'Form submitted successfully']);
    }
}
