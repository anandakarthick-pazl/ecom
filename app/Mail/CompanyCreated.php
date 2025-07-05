<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\Company;

class CompanyCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $email;
    public $password;

    public function __construct(Company $company, $email, $password)
    {
        $this->company = $company;
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Welcome to Your New E-Commerce Store!')
                    ->view('emails.company-created')
                    ->with([
                        'company' => $this->company,
                        'email' => $this->email,
                        'password' => $this->password,
                        'loginUrl' => url('/admin/login')
                    ]);
    }
}
