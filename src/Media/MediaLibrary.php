<?php

namespace Rhaima\VoltPanel\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Rhaima\VoltPanel\Models\Media;

class MediaLibrary
{
    protected string $disk;
    protected string $path;

    public function __construct()
    {
        $this->disk = config('voltpanel.media.disk', 'public');
        $this->path = config('voltpanel.media.path', 'media');
    }

    public function upload(UploadedFile $file, ?string $collection = 'default', ?array $metadata = []): Media
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($this->path . '/' . $collection, $filename, $this->disk);

        return Media::create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => $this->disk,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'collection' => $collection,
            'metadata' => $metadata,
            'user_id' => auth()->id(),
        ]);
    }

    public function uploadMultiple(array $files, ?string $collection = 'default'): array
    {
        return array_map(fn($file) => $this->upload($file, $collection), $files);
    }

    public function delete(int $mediaId): bool
    {
        $media = Media::find($mediaId);

        if (!$media) {
            return false;
        }

        Storage::disk($media->disk)->delete($media->path);

        return $media->delete();
    }

    public function getUrl(Media $media): string
    {
        return Storage::disk($media->disk)->url($media->path);
    }

    protected function generateFilename(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }

    public function getMediaByCollection(string $collection)
    {
        return Media::where('collection', $collection)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }
}
