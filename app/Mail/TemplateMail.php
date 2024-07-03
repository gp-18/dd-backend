<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $templateName;
    public $templateContent;
    public $dynamicData;

    public function __construct($templateName, $templateContent, $dynamicData)
    {
        $this->templateName = $templateName;
        $this->templateContent = $templateContent;
        $this->dynamicData = $dynamicData;
    }

    public function build()
    {
        $content = $this->replacePlaceholders($this->templateContent, $this->dynamicData);

        return $this->view('template')->subject($this->templateName)
            ->with([
                'templateName' => $this->templateName,
                'content' => $content,
            ]);
    }

    private function replacePlaceholders($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }
        return $content;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Template Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'template',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
