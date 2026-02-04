<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetoranBaruNotification extends Notification
{
    use Queueable;
    public $setoran;

    /**
     * Create a new notification instance.
     */
    public function __construct($setoran)
    {
        $this->setoran = $setoran;
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
        return [
            'setoran_id' => $this->setoran->id,
            'title'      => 'Setoran Baru Masuk!',
            'message'    => 'User ' . $this->setoran->user->name . ' membuat permintaan ' . $this->setoran->metode . '.',
            'link'       => route('petugas.setoran.show', $this->setoran->id),
            'icon'       => 'fa-box-open',
            'color'      => 'text-primary',
        ];
    }
}
