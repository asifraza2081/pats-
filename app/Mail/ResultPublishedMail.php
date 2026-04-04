<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Result Declared - ' . $this->application->job->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.results.published',
            with: [
                'name'        => $this->application->candidate->user->full_name,
                'jobTitle'    => $this->application->job->title,
                'projectName' => $this->application->project->name,
                'rollNo'      => $this->application->result->roll_no,
                'score'       => $this->application->result->score,
                'totalMarks'  => $this->application->result->total_marks,
                'percentage'  => $this->application->result->percentage,
                'percentile'  => $this->application->result->percentile,
                'url'         => route('results.search'),
            ],
        );
    }
}
