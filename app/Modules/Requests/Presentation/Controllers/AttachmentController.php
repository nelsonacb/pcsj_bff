<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Controllers;

use App\Modules\Requests\Infrastructure\Models\PortalAttachment;
use App\Modules\Requests\Presentation\Requests\StoreAttachmentRequest;
use App\Modules\Requests\Presentation\Resources\PortalAttachmentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController
{

    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $citizen = $request->user();

        $draft = $citizen->drafts()->findOrFail($data['draft_id']);

        $file      = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $storedName = Str::uuid() . '.' . $extension;
        $path = $file->storeAs(
            "attachments/{$citizen->id}",
            $storedName,
            'local'
        );

        $checksum = hash_file('sha256', Storage::disk('local')->path($path));

        $attachment = new PortalAttachment();
        $attachment->draft_id      = $draft->id;
        $attachment->filename      = $storedName;
        $attachment->original_name = $file->getClientOriginalName();
        $attachment->mime_type     = $file->getMimeType();
        $attachment->size          = $file->getSize();
        $attachment->storage_path  = $path;
        $attachment->checksum      = $checksum;
        $attachment->save();

        return (new PortalAttachmentResource($attachment))
            ->response()
            ->setStatusCode(201);
    }


    public function show(Request $request, int $id)
    {
        $attachment = $this->findOwnedAttachment($request, $id);

        $fullPath = Storage::disk('local')->path($attachment->storage_path);

        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'File not found on the server.'], 404);
        }

        return response()->download($fullPath, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
        ]);
    }


    public function destroy(Request $request, int $id): JsonResponse
    {
        $attachment = $this->findOwnedAttachment($request, $id);

        Storage::disk('local')->delete($attachment->storage_path);

        $attachment->delete();

        return response()->json(['message' => 'File deleted.']);
    }

    private function findOwnedAttachment(Request $request, int $id): PortalAttachment
    {
        $citizen = $request->user();

        return PortalAttachment::whereHas('draft', function ($q) use ($citizen) {
            $q->where('citizen_id', $citizen->id);
        })->findOrFail($id);
    }
}