<?php

namespace Rhaima\VoltPanel\Forms\Components;

class FileUpload extends Component
{
    protected bool $multiple = false;
    protected ?array $acceptedFileTypes = null;
    protected ?int $maxSize = null; // in KB
    protected bool $image = false;
    protected ?int $maxFiles = null;
    protected bool $reorderable = false;
    protected bool $downloadable = true;
    protected bool $previewable = true;
    protected ?string $directory = null;
    protected ?string $disk = 'public';
    protected ?string $visibility = 'public';

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function maxSize(int $sizeInKilobytes): static
    {
        $this->maxSize = $sizeInKilobytes;

        return $this;
    }

    public function image(): static
    {
        $this->image = true;
        $this->acceptedFileTypes = ['image/*'];

        return $this;
    }

    public function maxFiles(int $count): static
    {
        $this->maxFiles = $count;

        return $this;
    }

    public function reorderable(bool $reorderable = true): static
    {
        $this->reorderable = $reorderable;

        return $this;
    }

    public function downloadable(bool $downloadable = true): static
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    public function previewable(bool $previewable = true): static
    {
        $this->previewable = $previewable;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function visibility(string $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getType(): string
    {
        return 'file-upload';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'multiple' => $this->multiple,
            'acceptedFileTypes' => $this->acceptedFileTypes,
            'maxSize' => $this->maxSize,
            'image' => $this->image,
            'maxFiles' => $this->maxFiles,
            'reorderable' => $this->reorderable,
            'downloadable' => $this->downloadable,
            'previewable' => $this->previewable,
            'directory' => $this->directory,
            'disk' => $this->disk,
            'visibility' => $this->visibility,
        ]);
    }
}
