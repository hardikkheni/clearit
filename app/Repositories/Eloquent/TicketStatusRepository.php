<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasDisplayOrderColumn;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\DB;

class TicketStatusRepository extends BaseRepository
{

    use HasDisplayOrderColumn;

    const MODEL_LABEL = 'Ticket Status';

    public function __construct(
        TicketStatus $model
    ) {
        parent::__construct($model);
    }

    /**
     * @param array  $ticket
     */
    public function getHexColorByTicket($ticket)
    {
        $ticketConstants = config('constants.ticket');
        $defaultHexColor = $ticketConstants['DEFAULT_HEX_COLOR'];
        if (
            !$ticket['status']
            || !$ticket['type']
            || $ticket['type'] == $ticketConstants['type']['CLEARANCE'] && !$ticket['transport']
        ) {
            return $defaultHexColor;
        }

        $query = $this->model->query();
        $query->from('ticket_status as ts')->select(['ts.hexcolor']);
        $query->whereRaw('ts.statusname = "' . $ticket['status'] . '"');
        $query->whereRaw('ts.tickettype = "' . $ticket['type'] . '"');
        if ($ticket['type'] == $ticketConstants['type']['CLEARANCE']) {
            $query->whereRaw('ts.tickettransport = "' . $ticket['transport'] . '"');
        }
        if (!$ticket['substatus']) {
            $query->whereRaw('(ts.substatus IS NULL OR ts.substatus = "")');
        } else {
            $query->whereRaw('(ts.substatus = "' . $ticket['substatus'] . '" OR NOT EXISTS
            (SELECT 1 FROM ticket_status ts2 WHERE ts2.statusname = "' . $ticket['status'] . '" AND ts2.substatus = "' . $ticket['substatus'] . '")
            AND (ts.substatus IS NULL OR ts.substatus = ""))');
        }
        $row = $query->first();
        if ($row && $row->hexcolor) return $row->hexcolor;
        return $defaultHexColor;
    }


    /**
     * @param array  $ticket
     */
    public function getTextHexColorByTicket($ticket)
    {
        $ticketConstants = config('constants.ticket');
        $defaultTextHexColor = $ticketConstants['DEFAULT_TEXT_HEX_COLOR'];
        if (
            !$ticket['status']
            || !$ticket['type']
            || $ticket['type'] == $ticketConstants['type']['CLEARANCE'] && !$ticket['transport']
        ) {
            return $defaultTextHexColor;
        }

        $query = $this->model->query();
        $query->from('ticket_status as ts')->select(['ts.texthexcolor']);
        $query->whereRaw('ts.statusname = "' . $ticket['status'] . '"');
        $query->whereRaw('ts.tickettype = "' . $ticket['type'] . '"');
        if ($ticket['type'] == $ticketConstants['type']['CLEARANCE']) {
            $query->whereRaw('ts.tickettransport = "' . $ticket['transport'] . '"');
        }
        if (!$ticket['substatus']) {
            $query->whereRaw('(ts.substatus IS NULL OR ts.substatus = "")');
        } else {
            $query->whereRaw('(ts.substatus = "' . $ticket['substatus'] . '" OR NOT EXISTS
            (SELECT 1 FROM ticket_status ts2 WHERE ts2.statusname = "' . $ticket['status'] . '" AND ts2.substatus = "' . $ticket['substatus'] . '")
            AND (ts.substatus IS NULL OR ts.substatus = ""))');
        }
        $row = $query->first();
        if ($row && $row->texthexcolor) return $row->texthexcolor;
        return $defaultTextHexColor;
    }

    public function toDoTicketItemListByStatusId($id)
    {
        return $this->findOrFail($id)->toDoTicketItems()->get();
    }

    public function syncTodoTicketItemsByStatusId($id, $ids, $fresh = false)
    {
        if ($fresh) {
            return $this->findOrFail($id)->toDoTicketItems()->sync($ids);
        }
        return $this->findOrFail($id)->toDoTicketItems()->attach($ids);
    }

    public function delete($id)
    {
        $todo = $this->findOrFail($id);
        $todo->delete();
        $this->shiftFromDisplayOrder(+$todo->displayOrder, -1);
        return $todo;
    }

    public function groupTicketStatusesByStatusName($ticketStatusList)
    {
        $list = [];
        foreach ($ticketStatusList as $ticketStatus) {
            $index = array_search(
                $ticketStatus['statusName'],
                array_column($list, 'statusName')
            );
            if ($index !== false) {
                if ($ticketStatus['substatus']) {
                    $list[$index]['children'][] = [
                        'id' => $ticketStatus['id'],
                        'statusName' => $ticketStatus['substatus'],
                    ];
                } else {
                    $list[$index]['id'] = $ticketStatus['id'];
                }
            } else {
                $list[] = [
                    'id' => $ticketStatus['id'],
                    'statusName' => $ticketStatus['statusName'],
                    'children' => [
                        ...($ticketStatus['substatus'] ? [
                            [
                                'id' => $ticketStatus['id'],
                                'statusName' => $ticketStatus['substatus'],
                            ]
                        ] : [])
                    ]
                ];
            }
        }
        return $list;
    }
}
