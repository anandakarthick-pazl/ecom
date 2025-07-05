<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\SupportTicket;

class TicketStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Support Ticket Status Updated - #' . $this->ticket->id)
                    ->view('emails.ticket-status-updated')
                    ->with('ticket', $this->ticket);
    }
}
