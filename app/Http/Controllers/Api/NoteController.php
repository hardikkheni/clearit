<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Requests\Api\Note\CreateNoteRequest;
use App\Services\NoteService;
use Illuminate\Http\Request;

class NoteController extends BaseApiController
{

    protected NoteService $noteService;

    public function __construct(
        NoteService $noteService
    ) {
        $this->noteService = $noteService;
    }

    public function create(CreateNoteRequest $request)
    {
        $data = $this->noteService->create($request->validated());
        return $this->respond($data, HttpStatuses::HTTP_CREATED, "Note successfully created!.");
    }
}
