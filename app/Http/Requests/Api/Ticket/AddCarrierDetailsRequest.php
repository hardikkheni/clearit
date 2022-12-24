<?php

namespace App\Http\Requests\Api\Ticket;

use App\Http\Requests\Api\BaseFormRequest;

class AddCarrierDetailsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ISFConsolidatorContact_id' => '',
            'ISFConsolidator_id' => '',
            'SBentryNum' => '',
            'SBfilerCode' => '',
            'carrier' => '',
            'deliveryAddress' => '',
            'deliverySelection' => '',
            'haveLoadingDock' => 'boolean',
            'isfcEmailAddress' => '',
            'isfcMobilePhone' => '',
            'isfcName' => '',
            'isffiled' => 'boolean',
            'isfFiledOn' => '',
            'requiresDelivery' => 'boolean',
            'requiresLiftGate' => 'boolean',
            'transport' => 'required',
            'vendor_carrier_ref' => '',
        ];
    }
}
