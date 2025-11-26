<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFileEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $recipient;
    public $subject;
    public $body;
    public $fromEmail;
    public $fromName;

    /**
     * Create a new job instance.
     */
    public function __construct($recipient, $subject, $body, $fromEmail, $fromName)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->body = $body;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::html(
            view('emails.template', [
                'subject' => $this->subject,
                'body' => $this->body
            ])->render(),
            function ($message) {
                $message->to($this->recipient)
                        ->from($this->fromEmail, $this->fromName)
                        ->subject($this->subject);
            }
        );
    }


}
