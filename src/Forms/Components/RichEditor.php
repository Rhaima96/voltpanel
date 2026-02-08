<?php

namespace Rhaima\VoltPanel\Forms\Components;

class RichEditor extends Component
{
    protected ?array $toolbarButtons = null;
    protected bool $fileAttachments = false;
    protected ?string $fileAttachmentsDisk = null;
    protected ?string $fileAttachmentsDirectory = null;
    protected ?string $fileAttachmentsVisibility = 'public';
    protected ?int $maxContentLength = null;

    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    public function disableToolbarButtons(array $buttons): static
    {
        $defaultButtons = [
            'bold', 'italic', 'strike', 'link',
            'heading', 'bulletList', 'orderedList',
            'blockquote', 'codeBlock', 'undo', 'redo',
        ];

        $this->toolbarButtons = array_diff($defaultButtons, $buttons);

        return $this;
    }

    public function fileAttachments(bool $enabled = true): static
    {
        $this->fileAttachments = $enabled;

        return $this;
    }

    public function fileAttachmentsDisk(string $disk): static
    {
        $this->fileAttachmentsDisk = $disk;

        return $this;
    }

    public function fileAttachmentsDirectory(string $directory): static
    {
        $this->fileAttachmentsDirectory = $directory;

        return $this;
    }

    public function fileAttachmentsVisibility(string $visibility): static
    {
        $this->fileAttachmentsVisibility = $visibility;

        return $this;
    }

    public function maxContentLength(int $length): static
    {
        $this->maxContentLength = $length;

        return $this;
    }

    public function getType(): string
    {
        return 'rich-editor';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'toolbarButtons' => $this->toolbarButtons,
            'fileAttachments' => $this->fileAttachments,
            'fileAttachmentsDisk' => $this->fileAttachmentsDisk,
            'fileAttachmentsDirectory' => $this->fileAttachmentsDirectory,
            'fileAttachmentsVisibility' => $this->fileAttachmentsVisibility,
            'maxContentLength' => $this->maxContentLength,
        ]);
    }
}
