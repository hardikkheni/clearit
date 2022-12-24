<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\PgaRequest;
use Illuminate\Support\Facades\DB;

class PgaRequestRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Pga Request';

    public function __construct(
        PgaRequest $model
    ) {
        parent::__construct($model);
    }

    public function getPgaRequestListByTicketId($ticketId)
    {
        return $this->model->from('pga_request as pr')->join('pga as p', 'p.id', 'pr.pgaid')
            ->where('pr.ticketId', $ticketId)
            ->select([
                'pr.*',
                'p.name as document_name',
                DB::raw('IF(pr.isSigned = 1, \'Complete\', \'Pending\') as status')
            ])->get();
    }

    public function delete($id)
    {
        $row = $this->findOrFail($id);
        $row->delete();
        return $row;
    }
}
