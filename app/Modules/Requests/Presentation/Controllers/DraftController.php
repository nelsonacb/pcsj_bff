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

    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->drafts()->orderByDesc('last_saved_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return PortalDraftResource::collection($query->get())->response();
    }

 
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


    public function show(Request $request, int $id): JsonResponse
    {
        $draft = $request->user()->drafts()->findOrFail($id);

        return (new PortalDraftResource($draft))->response();
    }


    public function update(UpdateDraftRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $draft = $request->user()->drafts()->findOrFail($id);
        $draft->payload       = $data['payload'];
        $draft->last_saved_at = now();
        $draft->save();

        return (new PortalDraftResource($draft))->response();
    }


    public function destroy(Request $request, int $id): JsonResponse
    {
        $draft = $request->user()->drafts()->findOrFail($id);
        $draft->delete();

        return response()->json(['message' => 'Draft deleted.']);
    }
}