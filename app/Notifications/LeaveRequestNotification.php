<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Leave;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    protected $leave;

    /**
     * ទទួលយក Object Leave នៅពេល Trigger Notification
     */
    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    /**
     * កំណត់ Channel សម្រាប់ការផ្ញើ (ប្រើ database ដើម្បីបង្ហាញលើ Web)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * ទិន្នន័យដែលត្រូវរក្សាទុកក្នុង Table notifications
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'leave_id'      => $this->leave->id,
            'employee_name' => $this->leave->employee->name_en, // ប្តូរមកប្រើ name_en
            'duration'      => $this->leave->total_days,
            'title'         => $this->leave->employee->name_en, // ប្តូរមកប្រើ name_en សម្រាប់ Title
            'message'       => "has submitted a leave request for {$this->leave->total_days} day(s).",
            'link'          => '/admin/leaves',
            'type'          => 'request'
        ];
    }

    /**
     * ទុកវាចោលក៏បាន ប៉ុន្តែវាចាំបាច់សម្រាប់ Laravel
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
