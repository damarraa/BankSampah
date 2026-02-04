<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusSetoranNotification extends Notification
{
    use Queueable;
    public $setoran;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($setoran)
    {
        $this->setoran = $setoran;
        $this->status  = $setoran->status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $pesan = match ($this->status) {
            'diproses' => 'Petugas sedang menuju lokasi Anda.',
            'selesai'  => 'Setoran selesai! Saldo telah ditambahkan.',
            'ditolak'  => 'Maaf, setoran Anda dibatalkan.',
            default    => 'Status setoran diperbarui.'
        };

        $color = match ($this->status) {
            'selesai' => 'text-green-500',
            'ditolak' => 'text-red-500',
            default   => 'text-blue-500'
        };

        return [
            'setoran_id' => $this->setoran->id,
            'title'      => 'Status Setoran #' . $this->setoran->id,
            'message'    => $pesan,
            'link'       => route('user.setoran.show', $this->setoran->id),
            'icon'       => 'fa-info-circle',
            'color'      => $color,
        ];
    }
}
