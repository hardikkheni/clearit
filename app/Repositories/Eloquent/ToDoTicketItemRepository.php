<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasDisplayOrderColumn;
use App\Models\ToDoTicketItem;

class ToDoTicketItemRepository extends BaseRepository
{
    use HasDisplayOrderColumn;

    const MODEL_LABEL = 'To Do Ticket Item';

    public function __construct(ToDoTicketItem $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $todo = $this->findOrFail($id);
        $todo->delete();
        $this->shiftFromDisplayOrder(+$todo->displayOrder, -1);
        return $todo;
    }

    public function getUncheckedToDoTicketItemList($where = [], $sorts = [], $ticktId)
    {
        return $this->findAndOrSort($where, $sorts)->from('to_do_ticket_item AS tdti')->whereRaw("NOT EXISTS(SELECT 1 FROM to_do_ticket_item_completed as tdtic WHERE tdtic.todoticketitemid = tdti.id AND tdtic.ticketid = $ticktId)")->get();
    }
}
