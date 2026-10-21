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
    /**
     * POST /api/v1/portal/attachments
     * Sube un archivo y lo asocia a un borrador del ciudadano.
     */
    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $citizen = $request->user();

        // 1. Verificar ownership: el borrador debe ser del ciudadano
        $draft = $citizen->drafts()->findOrFail($data['draft_id']);

        // 2. Guardar el archivo en storage/app/attachments/{citizenId}/{uuid}.{ext}
        $file      = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $storedName = Str::uuid() . '.' . $extension;
        $path = $file->storeAs(
            "attachments/{$citizen->id}",
            $storedName,
            'local'
        );

        // 3. Calcular checksum (SHA-256) para integridad
        $checksum = hash_file('sha256', Storage::disk('local')->path($path));

        // 4. Crear el registro en la DB
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

    /**
     * GET /api/v1/portal/attachments/{id}
     * Descarga el archivo si pertenece a un borrador del ciudadano.
     */
    public function show(Request $request, int $id)
    {
        $attachment = $this->findOwnedAttachment($request, $id);

        $fullPath = Storage::disk('local')->path($attachment->storage_path);

        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'Archivo no encontrado en el servidor.'], 404);
        }

        return response()->download($fullPath, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
        ]);
    }

    /**
     * DELETE /api/v1/portal/attachments/{id}
     * Borra el archivo del disco y el registro de la DB.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $attachment = $this->findOwnedAttachment($request, $id);

        // 1. Borrar del disco
        Storage::disk('local')->delete($attachment->storage_path);

        // 2. Borrar de la DB (hard delete, no soft: los archivos no se "recuperan")
        $attachment->delete();

        return response()->json(['message' => 'Archivo eliminado.']);
    }

    /**
     * Busca el attachment verificando que pertenezca a un draft del ciudadano autenticado.
     * Devuelve 404 si no es suyo (no 403, para no revelar existencia).
     */
    private function findOwnedAttachment(Request $request, int $id): PortalAttachment
    {
        $citizen = $request->user();

        return PortalAttachment::whereHas('draft', function ($q) use ($citizen) {
            $q->where('citizen_id', $citizen->id);
        })->findOrFail($id);
    }
}