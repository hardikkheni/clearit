<?php

namespace App\Repositories\Eloquent;

use App\Models\Reminder;
use Illuminate\Support\Facades\DB;

class ReminderRepository extends BaseRepository
{

    const MODEL_LABEL = 'Reminder';

    public function __construct(Reminder $model)
    {
        parent::__construct($model);
    }

    public function getCheckedReminderList($ticketId, $where = [], $sorts = [])
    {
        $userId = auth()->user()->id;
        $q = $this->findAndOrSort($where, $sorts);
        $q->where('ticketId', $ticketId);
        $q->whereRaw('completedOn IS NOT NULL');
        $q->select([
            '*',
            DB::raw("DATE_FORMAT(getAdjustedDatetime(`reminder`.`dueOn`, $userId), '%m/%d/%Y') as dueon_format_date"),
            DB::raw("LOWER(DATE_FORMAT(getAdjustedDatetime(`reminder`.`dueOn`, $userId), '%l:%i%p')) as dueon_format_time"),
            DB::raw("DATE_FORMAT(getAdjustedDatetime(`reminder`.`completedon`, $userId), '%m/%d/%Y') as completedon_format"),
            DB::raw("CONCAT(u1.firstname, \" \", u1.lastname) as completed_by_user")
        ]);
        $q->leftJoin('user as u1', 'reminder.assignedToUserId', 'u1.id');
        $rows = $q->get();
        foreach ($rows as $row) {
            if ($row->dueon_format_time || $row->dueon_format_time == '12:00am') {
                $row->dueon_format_time = 'Anytime';
            }
        }
        return $rows;
    }

    public function getMyReminders($agentId)
    {
        $entities = array();
        $data = $this->call('getAgentReminders', [
            $agentId
        ]);
        foreach ($data[0] as $row) {
            if (empty($row->dueon_format_time) || $row->dueon_format_time == '12:00am') {
                $row->dueon_format_time = 'Anytime';
            }
            $entities[] = $row;
        } // Any time (00:00)
        return $entities;
    }

    public function edit($id, $data)
    {
        if (!is_array($id))
            return $this->model->where('id', $id)->update($data);
        else
            return $this->model->whereIn('id', $id)->update($data);
    }

    public function delete($id)
    {
        $reminder = $this->findOrFail($id);
        $reminder->delete();
        return $reminder;
    }

    public function findOrFail($id)
    {
        return $this->model->where(['id' => $id])->firstOrFail();
    }

    public function getPendingReminderList($ticketId, $where = [], $sorts = [])
    {
        $userId = auth()->user()->id;
        $query = $this->findAndOrSort($where, $sorts)->from('reminder as r');
        $query->leftJoin('user as u1', 'r.assignedToUserId', 'u1.id');
        $query->leftJoin('user as u2', 'r.createdBy', 'u2.id');
        $query->leftJoin('user as u3', 'r.modifiedBy', 'u3.id');
        $query->whereNull('r.completedOn');
        $query->where('r.ticketId', $ticketId);
        $query->select([
            'r.*',
            DB::raw('CONCAT(u1.firstname, " ", u1.lastname) as assigned_to'),
            DB::raw('CONCAT(u2.firstname, " ", u2.lastname) as created_by'),
            DB::raw('CONCAT(u3.firstname, " ", u3.lastname) as modified_by'),

            DB::raw('DATE_FORMAT(' . self::GetAdjustedDatetimeFunction . '(r.createdOn, ' . $userId . '), \'%m/%d/%Y\') as createdon_format_date'),
            DB::raw('DATE_FORMAT(' . self::GetAdjustedDatetimeFunction . '(r.modifiedOn, ' . $userId . '), \'%m/%d/%Y\') as modifiedon_format_date'),

            DB::raw('DATE_FORMAT(' . self::GetAdjustedDatetimeFunction . '(r.dueOn, ' . $userId . '), \'%m/%d/%Y\') as dueon_format_date'),
            DB::raw('DATE_FORMAT(' . self::GetAdjustedDatetimeFunction . '(r.dueOn, ' . $userId . '), \'%Y-%m-%d\') as dueon_format_date_y_m_d'),
            DB::raw('DATE_FORMAT(' . self::GetAdjustedDatetimeFunction . '(r.dueOn, ' . $userId . '), \'%l:%i%p\') as dueon_format_time'),

            DB::raw('DATE(NOW()) > DATE(' . self::GetAdjustedDatetimeFunction . '(r.dueOn,' . $userId . ')) as is_past_due'),
            DB::raw('DATE(NOW()) = DATE(' . self::GetAdjustedDatetimeFunction . '(r.dueOn,' . $userId . ')) as is_today_due'),
            DB::raw('(DATE(' . self::GetAdjustedDatetimeFunction . '(r.dueOn,' . $userId . ')) = DATE(DATE_ADD(NOW(), INTERVAL 1 DAY))) as is_tomorrow_due'),

        ]);
        $list = $query->get();
        foreach ($list as $row) {
            if (!$row->dueon_format_time || $row->dueon_format_time == '12:00am') {
                $row->dueon_format_time = 'Anytime';
            }
        }
        return $list;
    }
}
