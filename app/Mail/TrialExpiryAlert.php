<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\Company;

class TrialExpiryAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $daysRemaining;

    public function __construct(Company $company, $daysRemaining)
    {
        $this->company = $company;
        $this->daysRemaining = $daysRemaining;
    }

    public function build()
    {
        $subject = $this->daysRemaining > 0 
            ? "Trial Expires in {$this->daysRemaining} days - {$this->company->name}"
            : "Trial Expired - {$this->company->name}";

        return $this->subject($subject)
                    ->view('emails.trial-expiry-alert')
                    ->with([
                        'company' => $this->company,
                        'daysRemaining' => $this->daysRemaining
                    ]);
    }
}
