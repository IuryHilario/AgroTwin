<?php

namespace App\Mail;

use App\Models\Insumo;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RelatorioInsumoMail extends Mailable
{
    public function __construct(
        public Insumo $insumo,
        public array $relatorio,
        public string $pdf
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Relatório de {$this->insumo->ds_nome} - AgroTwin",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.relatorio-insumo',
            with: [
                'insumo' => $this->insumo,
                'relatorio' => $this->relatorio,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $this->pdf, "relatorio-{$this->insumo->ds_nome}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
