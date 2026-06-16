<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * បង្ហាញទំព័រ Admin សម្រាប់មើល និង បោះពុម្ព (Print) QR Code របស់ក្រុមហ៊ុន
     */
    public function showOfficeQr()
    {
        // កូដសម្ងាត់ដែលត្រូវនឹងកូដក្នុងទំព័រ Scan របស់បុគ្គលិក (Static QR)
        $qrValue = 'COMPANY_OFFICE_QR_2026';

        // ហៅប្រើប្រាស់ Free API របស់ Google ដើម្បីបង្កើតរូបភាព QR Code (ទំហំ 300x300px)
        $qrCodeUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.urlencode($qrValue).'&choe=UTF-8';

        // បោះទិន្នន័យ URL ទៅកាន់ផ្ទាំង Vue 3 របស់ Admin
        return Inertia::render('Admin/Attendance/OfficeQr', [
            'qrCodeUrl' => $qrCodeUrl,
            'qrValue' => $qrValue,
        ]);
    }
}
