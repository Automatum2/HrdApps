<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Training;

class TrainingAssignedNotification extends Notification
{
    use Queueable;

    public $training;

    /**
     * Create a new notification instance.
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // For now, just save to database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Tugas Pelatihan Baru: ' . $this->training->nama_training,
            'message' => 'Anda telah ditugaskan untuk mengikuti pelatihan ' . $this->training->nama_training . '. Skill yang dipelajari: ' . $this->training->skill_dipelajari . '. Waktu: ' . $this->training->tanggal_mulai . ' s/d ' . $this->training->tanggal_selesai,
            'action_url' => route('backoffice.dashboard'),
            'type' => 'training_assigned',
            'related_id' => $this->training->id
        ];
    }
}
