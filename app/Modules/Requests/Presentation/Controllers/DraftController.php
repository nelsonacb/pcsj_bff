<?php

declare(strict_types=1);

namespace App\Modules\Requests\Presentation\Controllers;

use App\Modules\Requests\Infrastructure\Models\PortalDraft;
use App\Modules\Requests\Presentation\Requests\StoreDraftRequest;
use App\Modules\Requests\Presentation\Requests\UpdateDraftRequest;
use App\Modules\Requests\Presentation\Resources\PortalDraftResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DraftController
{
    /**
     * GET /api/v1/portal/drafts?type=retirement_request
     * Lista los borradores del ciudadano autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->drafts()->orderByDesc('last_saved_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return PortalDraftResource::collection($query->get())->response();
    }

    /**
     * POST /api/v1/portal/drafts
     * Crea un nuevo borrador.
     */
    public function store(StoreDraftRequest $request): JsonResponse
    {
        $data = $request->validated();

        $draft = new PortalDraft();
        $draft->citizen_id    = $request->user()->id;
        $draft->type          = $data['type'];
        $draft->payload       = $data['payload'] ?? [];
        $draft->last_saved_at = now();
        $draft->save();

        return (new PortalDraftResource($draft))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/portal/drafts/{id}
     * Muestra un borrador del ciudadano autenticado.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $draft = $request->user()->drafts()->findOrFail($id);

        return (new PortalDraftResource($draft))->response();
    }

    /**
     * PATCH /api/v1/portal/drafts/{id}
     * Actualiza el contenido del borrador.
     */
    public function update(UpdateDraftRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $draft = $request->user()->drafts()->findOrFail($id);
        $draft->payload       = $data['payload'];
        $draft->last_saved_at = now();
        $draft->save();

        return (new PortalDraftResource($draft))->response();
    }

    /**
     * DELETE /api/v1/portal/drafts/{id}
     * Borra (soft delete) el borrador.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $draft = $request->user()->drafts()->findOrFail($id);
        $draft->delete();

        return response()->json(['message' => 'Borrador eliminado.']);
    }
}