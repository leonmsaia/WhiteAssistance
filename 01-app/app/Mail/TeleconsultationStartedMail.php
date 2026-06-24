<?php

namespace App\Mail;

use App\Modules\Appointments\Infrastructure\Models\Appointment;
use App\Modules\Teleconsultation\Infrastructure\Models\Teleconsultation;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TeleconsultationStartedMail extends Mailable
{
    public function __construct(
        public Appointment $appointment,
        public Teleconsultation $teleconsultation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Teleconsulta iniciada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teleconsultation-started',
        );
    }
}
