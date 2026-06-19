<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseAccessActivated extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?string $temporaryPassword;

    public function __construct(Order $order, ?string $temporaryPassword = null)
    {
        $this->order = $order;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function build()
    {
        return $this
            ->subject('Akses kelas Anda sudah aktif - ' . $this->order->course->title)
            ->markdown('emails.course-access-activated');
    }
}
