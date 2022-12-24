<?php

namespace App\Services\Helper;

use App\Mail\Affiliate\{
    AffiliateRegisteredLink,
    AffiliateCustomerContacted,
};
use App\Mail\Ticket\{
    NewTicketNotify,
    AffiliateTicketCreated,
};
use App\Models\{
    Affiliate,
    Ticket,
    UserAffiliateData
};
use App\Repositories\Eloquent\{
    AffiliateRepository,
    UserRepository,
    UserAffiliateDataRepository,
    UserSoldToRepository,
    UserVendorRepository,
    TicketRepository,
    TicketDocumentRepository,
    DocUploadTypeRepository,
    ConfigRepository,
};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class AffiliateService
{
    protected $affiliateRepo;
    protected $userRepo;
    protected $userAffiliateDataRepo;
    protected $userSoldToRepo;
    protected $userVendorRepo;
    protected $ticketRepo;
    protected $ticketDocumentRepo;
    protected $dutRepo;
    protected $configRepo;

    public function __construct(
        AffiliateRepository $affiliateRepo,
        UserRepository $userRepo,
        UserAffiliateDataRepository $userAffiliateDataRepo,
        UserSoldToRepository $userSoldToRepo,
        UserVendorRepository $userVendorRepo,
        TicketRepository $ticketRepo,
        DocUploadTypeRepository $dutRepo,
        TicketDocumentRepository $ticketDocumentRepo,
        ConfigRepository $configRepo
    )
    {
        $this->affiliateRepo = $affiliateRepo;
        $this->userRepo = $userRepo;
        $this->userAffiliateDataRepo = $userAffiliateDataRepo;
        $this->userSoldToRepo = $userSoldToRepo;
        $this->userVendorRepo = $userVendorRepo;
        $this->ticketRepo = $ticketRepo;
        $this->dutRepo = $dutRepo;
        $this->ticketDocumentRepo = $ticketDocumentRepo;
        $this->configRepo = $configRepo;
    }

    public function allAffiliates()
    {
        return $this->affiliateRepo->allAffiliates();
    }

    public function dataTable($data)
    {
        return $this->affiliateRepo->dataTable($data);
    }

    public function create($data)
    {
        $exist = $this->affiliateRepo->affiliateExistByCode($data['affiliateCode']);

        if ($exist) {
            throw ValidationException::withMessages([
                'affiliateCode' => "Affiliate Code alreday exists!"
            ]);
        }

        if (@$data['logofilename']) {
            $file = Storage::putFile(Affiliate::LOGOPATH, $data['logofilename']);
            $file = explode('/', $file);
            $file = $file[count($file) - 1];
            $data['logofilename'] = $file;
        } else {
            $data['logofilename'] = null;
        }
        return $this->affiliateRepo->create($data);
    }

    public function findOrFail($id)
    {
        return $this->affiliateRepo->findOrFail($id);
    }

    public function edit($id, $data)
    {
        $affiliate = $this->affiliateRepo->findOrFail($id);
        $exist = $this->affiliateRepo->affiliateExistByCode($data['affiliateCode']);
        if ($exist && $exist->id != $affiliate->id) {
            throw ValidationException::withMessages([
                'affiliateCode' => ['Affiliate Code alreday exists!']
            ]);
        }

        if (isset($data['remove_logo'])) {
            if (@$data['logofilename']) {
                $file = Storage::putFile(Affiliate::LOGOPATH, $data['logofilename']);
                $file = explode('/', $file);
                $file = $file[count($file) - 1];
                $data['logofilename'] = $file;
            } else {
                $data['logofilename'] = null;
            }
            unset($data['remove_logo']);
        }
        return $this->affiliateRepo->edit($id, $data);
    }

    public function delete($id)
    {
        return $this->affiliateRepo->delete($id);
    }

    public function registerAffiliate($data)
    {
        $affiliate = $this->affiliateRepo->findOrFail($data['affiliate_id']);
        $user = $this->userRepo->findByEmail($data['email']);
        if ($user) {
            $userAffiliateData = $this->userAffiliateDataRepo->getAffiliateDataByUser($data['shipment_number'], $user['id']);
            if ($userAffiliateData) {
                throw ValidationException::withMessages([
                    'shipment_number' => sprintf('Another agent has already initiated contact with the user associated with the reference number: %s.', $data['shipment_number'])
                ]);
            } else {
                $updateData = [];
                if (empty($user['affiliateReference']))
                    $updateData['affiliateReference'] = $data['business_key'];
                else if ($user['affiliateReference'] != $data['business_key'])
                    throw ValidationException::withMessages([
                        'business_key' => 'User already exists and has different affiliateReference'
                    ]);
                if (empty($user['affiliateShipmentNumber']))
                    $updateData['affiliateShipmentNumber'] = $data['shipment_number'];
                if (!empty($updateData))
                    $user = $this->userRepo->update(['id' => $user['id']], $updateData);
            }
        } else {
            $user = $this->userRepo->create($this->transformAffiliate($data));
            if ($user)
                $user = $this->userRepo->findById($user['id']);
        }
        if ($user) {
            $docs = [];
            if (@$data['doc_invoice_input']) {
                $file = strtotime("now") . '_' . rand(1, 1000) . '_' . $data['doc_invoice_input']->getClientOriginalName();
                Storage::putFileAs(UserAffiliateData::FILESPATH, $data['doc_invoice_input'], $file);
                $docs[] = [
                    'type' => config('constants.document_upload_type.type.COMMERCIAL_INVOICE'),
                    'file' => $file
                ];
            }
            if (@$data['doc_bill_of_lading_input']) {
                $file = strtotime("now") . '_' . rand(1, 1000) . '_' . $data['doc_bill_of_lading_input']->getClientOriginalName();
                Storage::putFileAs(UserAffiliateData::FILESPATH, $data['doc_bill_of_lading_input'], $file);
                $docs[] = [
                    'type' => config('constants.document_upload_type.type.BILL_OF_LADING'),
                    'file' => $file
                ];
            }
            if (@$data['doc_isf_input']) {
                $file = strtotime("now") . '_' . rand(1, 1000) . '_' . $data['doc_isf_input']->getClientOriginalName();
                Storage::putFileAs(UserAffiliateData::FILESPATH, $data['doc_isf_input'], $file);
                $docs[] = [
                    'type' => config('constants.document_upload_type.type.ISF'),
                    'file' => $file
                ];
            }
            if (@$data['doc_paps_input']) {
                $file = strtotime("now") . '_' . rand(1, 1000) . '_' . $data['doc_paps_input']->getClientOriginalName();
                Storage::putFileAs(UserAffiliateData::FILESPATH, $data['doc_paps_input'], $file);
                $docs[] = [
                    'type' => config('constants.document_upload_type.type.PAPS'),
                    'file' => $file
                ];
            }
            $affiliateData = [
                'affiliate_transport' => $data['transport'],
                'affiliate_docs' => $docs,
                'affiliate_bond' => $data['bond'],
                'affiliate_ref' => $data['shipment_number'],
            ];
            $userAffiliateData = $this->userAffiliateDataRepo->create(['affiliateData' => serialize($affiliateData),
                'affiliateReference' => $data['shipment_number'],
                'affiliateId' => $data['affiliate_id'],
                'userId' => $user['id']
            ]);
            if ($this->canUserStartTicket($user)) {

                $soldToData = ['userId' => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'address' => $user['address'],
                    'city' => $user['city'],
                    'country' => $user['country'],
                    'state' => $user['state'],
                    'zip' => $user['zip'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'fax' => $user['fax']
                ];
                if ($user['status'] == config('constants.user.USER_STATUS_PERSONAL')) {
                    $soldToData['taxid'] = $user['taxid'];
                }
                if ($user['status'] > config('constants.user.USER_STATUS_PERSONAL')) {
                    $soldToData['busname'] = $user['busname'];
                }
                $userSoldTo = $this->userSoldToRepo->create($soldToData);

                if (in_array($user['status'], config('constants.user.COMPANY_USER_STATUSES'))) {
                    $vendorName = $user['busname'];
                } else {
                    $vendorName = $user['firstname'] . ' ' . $user['lastname'];
                }
                $userVendor = $this->userVendorRepo->create(['userId' => $user['id'],
                    'vendorName' => $vendorName,
                    'vendorAddress' => $user['address'],
                    'vendorCity' => $user['city'],
                    'vendorCountry' => $user['country'],
                    'vendorState' => $user['state'],
                    'vendorZip' => $user['zip'],
                    'vendorEmail' => $user['email'],
                    'vendorPhone' => $user['phone'],
                    'vendorFax' => $user['fax']
                ]);

                $ticketData = ['status' => config('constants.ticket.status.NEW'),
                    'type' => config('constants.ticket.type.CLEARANCE'),
                    'guid' => strtoupper(Uuid::uuid4()),
                    'userid' => $user['id'],
                    'soldToId' => $userSoldTo['id'],
                    'vendorId' => $userVendor['id'],
                    'same_sold' => 1,
                    'same_vendor' => 1,
                    'isffiled' => 0,
                    'isAccepted' => 1,
                    'createdBy' => 3
                ];
                if (!empty($userAffiliateData['affiliateId'])) {
                    $ticketData['affiliateId'] = $userAffiliateData['affiliateId'];
                }
                if (!empty($affiliateData['affiliate_ref'])) {
                    $ticketData['affiliateReferenceNumber'] = $affiliateData['affiliate_ref'];
                }
                $ticket = $this->ticketRepo->create($ticketData);

                $this->userAffiliateDataRepo->update(['id' => $userAffiliateData['id']], ['ticketId' => $ticket['id']]);

                if (is_array($affiliateData['affiliate_docs'])) {
                    // Attach saved docs
                    foreach ($affiliateData['affiliate_docs'] as $doc) {
                        $filePath = UserAffiliateData::FILESPATH . $doc['file'];
                        $documentUploadTypeId = $this->dutRepo->getDocumentUploadTypeListByConstant($doc['type'], $affiliateData['affiliate_transport']);
                        if (Storage::exists($filePath)) {
                            // Move the file from tmp folder to the ticket document folder
                            $newFileName = $ticket['id'] . '_' . $doc['file'];
                            Storage::move($filePath, Ticket::DOCUMENTPATH.$newFileName);
                            $ticketDocument = [
                                'description' => $doc['type'],
                                'documentUploadTypeId' => $documentUploadTypeId > 0 ? $documentUploadTypeId : null,
                                'guid'        => strtoupper(Uuid::uuid4()),
                                'ticketId'    => $ticket['id'],
                                'userId'      => $user['id'],
                                'file'        => $newFileName,
                                'createdBy'   => 3,
                            ];
                            $this->ticketDocumentRepo->create($ticketDocument);
                        }
                    }
                    $config = $this->configRepo->findOne(['code' => config('constants.ticket.NEW_TICKET_EMAIL')]);
                    Mail::to($config['value'])
                        ->send(new NewTicketNotify($ticket));

                    Mail::to($user['email'])
                        ->send(new AffiliateTicketCreated([
                            'affiliateRef'  => $affiliateData['affiliate_ref'],
                            'companyname'   => $affiliate['companyname'],
                            'firstname'     => $user['firstname'],
                            'lastname'      => $user['lastname'],
                        ]));
                }
            } else {
                Mail::to($user['email'])
                    ->send(new AffiliateRegisteredLink([
                        'shipmentNumber' => $data['shipment_number'],
                        'companyname' => $affiliate['companyname'],
                        // TODO: add step2 link
                        'url' => ''
                    ]));
            }
            if ($affiliate['notificationEmail']) {
                Mail::to($affiliate['notificationEmail'])
                    ->send(new AffiliateCustomerContacted([
                        'shipmentNumber' => $data['shipment_number'],
                    ]));
            }
            if(@$ticket) {
                return ['ticket_id' => $this->ticketRepo->getTicketNumberFromId($ticket['id'])];
            }
            return true;
        }
    }

    public function transformAffiliate($data)
    {
        $data['guid'] = strtoupper(Uuid::uuid4());
        $data['login'] = $data['email'];

        $data['affiliateReference'] = $data['business_key'];
        unset($data['business_key']);

        $data['affiliateShipmentNumber'] = $data['shipment_number'];
        unset($data['shipment_number']);

        $data['affiliateId'] = $data['affiliate_id'];
        unset($data['affiliate_id']);

        $data['isReference'] = 1;
        $data['CreatedByUserId'] = 3;
        switch ($data['bond']) {
            case 1:
                $data['bondType'] = 2;
                $data['needBondVerify'] = 1;
                $data['isBondRequested'] = null;
                break;
            case 2:
                $data['bondType'] = 2;
                $data['needBondVerify'] = null;
                $data['isBondRequested'] = 1;
                $data['bondRequestDate'] = now();
                break;
            case 3:
                $data['bondType'] = 1;
                $data['needBondVerify'] = null;
                $data['isBondRequested'] = null;
                break;
            default:
                break;
        }
        return $data;
    }

    public function canUserStartTicket($user): bool
    {
        return true;
        if ($user['firstname'] &&
            $user['lastname'] &&
            $user['city'] &&
            $user['country'] &&
            $user['state'] &&
            $user['zip'] &&
            in_array($user['status'], config('constants.user.USER_STATUSES'))) {

            //Check phone number for CA version
            if(config('constants.config.DEFAULT_COUNTRY') == 'CA') {
                if (!$user['phone'])
                    return false;
                // GST number and Company name are required for CA Business accounts
                if ($user['status'] == config('constants.user.USER_STATUS_COMMERCAIL')
                    && (!$user['iannumber'] || !$user['busname'])) {
                    return false;
                }
            }

            //Check corporate and corporatetype for US version
            if($user['status'] != config('constants.user.USER_STATUS_PERSONAL') && config('constants.config.DEFAULT_COUNTRY') == 'US') {
                if($user['corporate'] && $user['corporateType'])
                    return true;
                else
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }
}
