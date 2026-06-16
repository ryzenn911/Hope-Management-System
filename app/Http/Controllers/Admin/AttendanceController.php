<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function showOfficeQr()
    {
        $qrValue = 'HOPE_ATTENDANCE';
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode($qrValue);

        return Inertia::render('Admin/Attendance/OfficeQr', [
            'qrCodeUrl' => $qrCodeUrl,
            'qrValue' => $qrValue,
        ]);
    }
}
