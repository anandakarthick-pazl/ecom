<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\SupportTicket;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('New Support Ticket Created - #' . $this->ticket->id)
                    ->view('emails.ticket-created')
                    ->with('ticket', $this->ticket);
    }
}
