<?php

namespace App\Mail;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CategoryCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Category $category) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kategori Baharu Telah Ditambah',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.category_created',
        );
    }
}
