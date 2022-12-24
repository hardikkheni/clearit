<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasDisplayOrderColumn;
use App\Models\AgentStatusType;
use Illuminate\Support\Facades\DB;

class AgentStatusTypeRepository extends BaseRepository
{
    use HasDisplayOrderColumn;

    const MODEL_LABEL = 'Agent Status Type';

    public function __construct(AgentStatusType $model)
    {
        parent::__construct($model);
    }

    public function getUsedAgentStatusTypeList()
    {
        return $this->model->query()->select([DB::raw("DISTINCT CONCAT(ast.`statusname`,' (',ast.`tickettype`,')') as name"), 'ast.*'])->from('agent_status_type AS ast')
            ->join('ticket AS t', 't.agentstatustypeid', 'ast.id')->get();
    }

    public function delete($id)
    {
        $todo = $this->findOrFail($id);
        $todo->delete();
        $this->shiftFromDisplayOrder(+$todo->displayOrder, -1);
        return $todo;
    }
}
