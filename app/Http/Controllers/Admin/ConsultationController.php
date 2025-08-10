<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConsultationRequest;
use App\Models\Consultation;
// use Illuminate\Support\Carbon;
use Mews\Purifier\Facades\Purifier;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::latest()->get();

        return view('admin.consultations.index', compact('consultations'));
    }
}
