<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\Message;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Message';

    protected $ticketRepo;

    public function __construct(
        Message $model,
        TicketRepository $ticketRepo
    ) {
        parent::__construct($model);
        $this->ticketRepo = $ticketRepo;
    }

    public function markViewed($ticketId)
    {
        return $this->update([
            'ticketid' => $ticketId,
            'isMaster' => false,
        ], ['isNew' => false]);
    }

    public function getMessageListByTicketId($ticketId)
    {
        $q = $this->findAndOrSort(['ticketId' => $ticketId], [['id', 'ASC']]);
        $q->select(['*', DB::raw('(SELECT `firstname` FROM `user` WHERE `id` = `message`.`userid`) as name')]);
        return $q->get();
    }

    public function saveMessage($msg)
    {
        if (($message = $this->findById(@$msg['id']))) {
            $message->save();
        } else {
            $msg['isNew'] = true;
            $message = $this->create($msg);
            $this->ticketRepo->update(['id' => $msg['ticketId']], ['isNew1' => $msg['userId']]);
        }
        return $message;
    }

    public function presetMessage($ticketId, $isFreightosUser = 0)
    {
        $config = config('constants.config');
        $ticketContants = config('constants.ticket');
        $service = $config['SERVICE_NAME'];
        $papsEmail = $config['PAPS_EMAIL'];

        $ticket = ($this->ticketRepo->findById($ticketId) ?? []);
        if ($config['DEFAULT_COUNTRY'] == 'CA') {
            $message = 'Welcome to ' . $service . ' and thank you for your submission. Your agent is currently '
                . 'reviewing your file and will respond to you here in just a few moments';
        } else {
            $message = 'Welcome to ' . $service . '. An agent is currently reviewing your submission and will send you a chat message shortly!';

            if ($isFreightosUser == 1) {
                if (!empty($ticket['transport']) && $ticket['type'] == $ticketContants['type']['CLEARANCE']) {
                    switch ($ticket['transport']) {
                        case $ticketContants['transport']['TRUCK']:
                        case $ticketContants['transport']['COURIER']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your submission and will message you shortly. In the meantime, please be sure "
                                . "you have completed the following:</p><ul><li>Upload Commercial invoice (bill of sale)</li>"
                                . "<li>Upload Bill of lading</li><li>Notify transportation company " . $service . " is your "
                                . "customs broker, they can forward their paperwork to "
                                . "<a href=\"mailto:" . $papsEmail . "\" target=\"_blank\">" . $papsEmail . "</a></li></ul>"
                                . "<p>If you have any questions or concerns, let us know right here and an agent will be "
                                . "right with you</p><p><strong><small>(Live Operators: Monday-Friday " . $config['WORKING_HOURS']
                                . " EST)</small></strong></p>";
                            break;
                        case $ticketContants['transport']['OCEAN']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your documentation and will message you if anything further is required.</p>"
                                . "<p>Your forwarder will provide Clearit with the required shipping documents once they "
                                . "become available.</p><p>including:</p><ul><li>Bill of lading</li>"
                                . "<li>ISF document</li><li>Arrival Notice</li></ul>"
                                . "<p>If you have any questions or concerns, please let us know right here and your agent "
                                . "will respond shortly.</p><p><strong><small>(Operating hours: Monday-Friday " . $config['WORKING_HOURS']
                                . " EST)</small></strong></p>";
                            break;
                        case $ticketContants['transport']['AIR']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your documentation and will message you if anything further is required.</p>"
                                . "<p>Your forwarder will provide Clearit with the required shipping documents once they "
                                . "become available.</p><p>including:</p><ul><li>Air Waybill</li>"
                                . "<li>Arrival Notice</li></ul><p>If you have any questions or concerns, please let us know right here and your "
                                . "agent will respond shortly.</p><p><strong><small>(Operating hours: Monday-Friday "
                                . $config['WORKING_HOURS'] . " EST)</small></strong></p>";
                            break;
                    }
                }
            } else {
                if (!empty($ticket['transport']) && $ticket['type'] == $ticketContants['type']['CLEARANCE']) {
                    switch ($ticket['transport']) {
                        case $ticketContants['transport']['TRUCK']:
                        case $ticketContants['transport']['COURIER']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your submission and will message you shortly. In the meantime, please be sure "
                                . "you have completed the following:</p><ul><li>Upload Commercial invoice (bill of sale)</li>"
                                . "<li>Upload Bill of lading</li><li>Notify transportation company " . $service . " is your "
                                . "customs broker, they can forward their paperwork to "
                                . "<a href=\"mailto:" . $papsEmail . "\" target=\"_blank\">" . $papsEmail . "</a></li></ul>"
                                . "<p>If you have any questions or concerns, let us know right here and an agent will be "
                                . "right with you</p><p><strong><small>(Live Operators: Monday-Friday " . $config['WORKING_HOURS']
                                . " EST)</small></strong></p>";
                            break;
                        case $ticketContants['transport']['OCEAN']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your submission and will message you shortly. In the meantime, please be sure "
                                . "you have completed the following:</p><ul><li>Upload Commercial invoice</li>"
                                . "<li>Upload Bill of lading</li><li>Upload ISF filling (or confirm it has already been filed "
                                . "on your behalf)</li><li>Arrival Notice (if goods have already arrived in the USA)</li></ul>"
                                . "<p>If you have any questions or concerns, let us know right here and an agent will be "
                                . "right with you</p><p><strong><small>(Live Operators: Monday-Friday " . $config['WORKING_HOURS']
                                . " EST)</small></strong></p>";
                            break;
                        case $ticketContants['transport']['AIR']:
                            $message = "<p>Welcome to " . $service . " and thank you for your submission. An Agent is currently "
                                . "reviewing your submission and will message you shortly. In the meantime, please be sure "
                                . "you have completed the following:</p><ul><li>Upload Commercial invoice (bill of sale)</li>"
                                . "<li>Upload Airway Bill</li><li>Upload Arrival Notice (if goods have already arrived in the "
                                . "USA)</li></ul><p>If you have any questions or concerns, let us know right here and an "
                                . "agent will be right with you</p><p><strong><small>(Live Operators: Monday-Friday "
                                . $config['WORKING_HOURS'] . " EST)</small></strong></p>";
                            break;
                    }
                }
            }
            if ($ticket['type'] == $ticketContants['type']['CUSTOM']) {
                $message = 'Welcome to ' . $service . ' consulting services. Please give us a brief description '
                    . 'of your needs below and upload any relevant documents. An agent will review your submission '
                    . 'and offer you a quote before proceeding.';
            }
        }

        $message = [
            'guid' => Str::upper(Str::uuid()),
            'message' => $message,
            'ticketId' => $ticketId,
            'isMaster' => 1,
            'userId' => 0,
            'messageFile' => '',
        ];

        return $this->saveMessage($message);
    }

    public function newMessage($data)
    {
        // $data['isNew1'] = true;
        return $this->create($data);
    }
}
