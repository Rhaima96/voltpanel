<?php

namespace Rhaima\VoltPanel\Tables\Actions;

class DeleteAction extends Action
{
    public ?string $color = 'red';
    public bool $requiresConfirmation = true;
    public ?string $confirmationTitle = null;
    public ?string $confirmationMessage = null;

    public function requiresConfirmation(bool $requiresConfirmation = true): static
    {
        $this->requiresConfirmation = $requiresConfirmation;

        return $this;
    }

    public function confirmationTitle(string $title): static
    {
        $this->confirmationTitle = $title;

        return $this;
    }

    public function confirmationMessage(string $message): static
    {
        $this->confirmationMessage = $message;

        return $this;
    }

    public function getType(): string
    {
        return 'delete';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'requiresConfirmation' => $this->requiresConfirmation,
            'confirmationTitle' => $this->confirmationTitle ?? 'Delete Record',
            'confirmationMessage' => $this->confirmationMessage ?? 'Are you sure you want to delete this record?',
        ]);
    }
}
