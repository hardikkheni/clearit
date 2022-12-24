<?php

namespace App\Repositories\Eloquent;

use App\Models\ToDoTicketItemCompleted;

class ToDoTicketItemCompletedRepository extends BaseRepository
{

    const MODEL_LABEL = 'To Do Ticket Item Completed';

    public function __construct(
        ToDoTicketItemCompleted $model
    ) {
        parent::__construct($model);
    }

    public function getCheckedToDoTicketItemsByTicket($where = [], $sorts = [], $ticketId)
    {
        $query = $this->findAndOrSort($where, $sorts)->from('to_do_ticket_item_completed as tdtic')->where("ticketId", $ticketId);
        $query->join('to_do_ticket_item as tdti', 'tdti.id', 'tdtic.toDoTicketItemId');
        $query->join('user as u', 'u.id', 'tdtic.modifiedBy');
        return $query->select(['tdti.*', 'tdtic.*', 'u.firstname', 'u.lastname'])->get();
    }
}
