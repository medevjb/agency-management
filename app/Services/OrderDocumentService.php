<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAudit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderDocumentService
{
    /**
     * Store the uploaded client documents and append them to the order.
     *
     * Metadata is kept per document (including the disk it lives on) so that
     * switching FILESYSTEM_DISK to S3 later keeps existing files reachable.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, array<string, mixed>> the documents that were stored
     */
    public function store(Order $order, array $files): array
    {
        if ($files === []) {
            return [];
        }

        $disk = $this->disk();
        $user = auth()->user();
        $stored = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store("orders/{$order->id}/documents", $disk);

            $stored[] = [
                'id' => (string) Str::uuid(),
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'disk' => $disk,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_at' => now()->toDateTimeString(),
                'uploaded_by' => $user?->id,
                'uploaded_by_name' => $user?->name,
            ];
        }

        if ($stored === []) {
            return [];
        }

        $order->forceFill([
            'client_documents' => array_merge($stored, $order->documents()),
        ])->save();

        OrderAudit::record($order, 'document_uploaded', [], [
            'documents' => array_column($stored, 'name'),
        ]);

        return $stored;
    }

    /**
     * Remove a single document from the order and from storage.
     *
     * @return array<string, mixed>|null the removed document
     */
    public function delete(Order $order, string $documentId): ?array
    {
        $document = $order->findDocument($documentId);

        if ($document === null) {
            return null;
        }

        Storage::disk($document['disk'] ?? $this->disk())->delete($document['path']);

        $remaining = array_values(array_filter(
            $order->documents(),
            fn (array $item) => $item['id'] !== $documentId
        ));

        $order->forceFill(['client_documents' => $remaining])->save();

        OrderAudit::record($order, 'document_deleted', ['documents' => [$document['name']]], []);

        return $document;
    }

    /**
     * The disk new uploads are written to.
     */
    public function disk(): string
    {
        return config('filesystems.default');
    }
}
