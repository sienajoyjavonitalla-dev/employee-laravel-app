<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobAssigned extends Mailable
{
    use Queueable, SerializesModels;

    protected $job_id;
    protected $job_title;
    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job_id, $job_title, $employee)
    {
        $this->job_id = $job_id;
        $this->job_title = $job_title;
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.jobAssigned',[
            'job_id' => $this->job_id,
            'job_title' => $this->job_title,
            'employee' => $this->employee
        ]);
    }
}
