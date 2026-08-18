<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\LeaveRequest;

class LeaveApprovedNotification extends Notification
{
    use Queueable;

    protected $leave;

    public function __construct(LeaveRequest $leave)
    {
        $this->leave = $leave;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'leave_approved',
            'title' => 'Cuti Disetujui',
            'message' => 'Pengajuan cuti Anda untuk tanggal ' . $this->leave->tanggal_mulai->format('d M Y') . ' s/d ' . $this->leave->tanggal_selesai->format('d M Y') . ' telah disetujui.',
            'leave_id' => $this->leave->id,
        ];
    }
}
