<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Leave;

class LeaveStatusNotification extends Notification
{
    use Queueable;

    protected $leave;

    /**
     * ទទួលយកទិន្នន័យ Leave ដែលបាន Update រួច
     */
    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    /**
     * កំណត់យក Channel 'database' ដើម្បីបង្ហាញលើ Web UI
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * រៀបចំទិន្នន័យសម្រាប់រក្សាទុកក្នុង Database
     */
    public function toDatabase(object $notifiable): array
    {
        // កំណត់ពណ៌ ឬ Icon តាម Status
        $statusLabel = ucfirst($this->leave->status);

        return [
            'leave_id' => $this->leave->id,
            'title' => 'Leave Request ' . $statusLabel,
            'message' => 'Your leave request from ' . $this->leave->start_date . ' has been ' . $this->leave->status . '.',
            'link' => '/staff/leaves',
            'type' => 'status_update',
            'status' => $this->leave->status
        ];
    }

    /**
     * ទុកវាចោលធម្មតា
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
