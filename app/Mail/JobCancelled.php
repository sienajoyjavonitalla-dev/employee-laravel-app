<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobCancelled extends Mailable
{
    use Queueable, SerializesModels;

    protected $job_id;
    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job_id, $employee)
    {
        $this->job_id = $job_id;
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.jobCancelled',[
            'job_id' => $this->job_id,
            'employee' => $this->employee
        ]);
    }
}
