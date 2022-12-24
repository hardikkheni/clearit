<?php

namespace App\Services;

use App\Models\Note;
use App\Repositories\Eloquent\NoteRepository;
use App\Repositories\Eloquent\TicketRepository;
use Illuminate\Support\Facades\Storage;


class NoteService
{

    protected NoteRepository $noteRepo;
    protected TicketRepository $ticketRepo;

    public function __construct(
        NoteRepository $noteRepo,
        TicketRepository $ticketRepo
    ) {
        $this->noteRepo = $noteRepo;
        $this->ticketRepo = $ticketRepo;
    }

    public function create($data)
    {
        $ticket = $this->ticketRepo->findOrFail($data['ticketId']);
        if (@$data['notefile']) {
            $file = Storage::putFile(Note::ATTACHMENTPATH, $data['notefile']);
            $file = explode('/', $file);
            $file = $file[count($file) - 1];
            $data['notefile'] = $file;
        } else {
            $data['notefile'] = null;
        }
        $note = $this->noteRepo->create($data);
        $note->load('user');
        return $note;
    }
}
