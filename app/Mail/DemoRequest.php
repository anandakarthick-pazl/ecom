<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuperAdmin\Theme;

class DemoRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $requestData;
    public $theme;

    public function __construct($requestData, Theme $theme)
    {
        $this->requestData = $requestData;
        $this->theme = $theme;
    }

    public function build()
    {
        return $this->subject('New Demo Request - ' . $this->theme->name)
                    ->view('emails.demo-request')
                    ->with([
                        'requestData' => $this->requestData,
                        'theme' => $this->theme
                    ]);
    }
}
