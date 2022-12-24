<?php

namespace App\Http\Requests\Api\Helper\Affiliate;

use App\Http\Requests\Api\BaseFormRequest;

class RegisterAffiliateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'email|required|max:255',
            'firstname' => '',
            'lastname' => '',
            'business_key' => '',
            'shipment_number' => '',
            'bond' => 'required|in:1,2,3',
            'transport' => 'required|in:'.implode(',', config('constants.ticket.transportModes')),
            'doc_invoice_input' => '',
            'doc_bill_of_lading_input' => '',
            'doc_isf_input' => '',
            'doc_paps_input' => '',
            'affiliate_id' => 'required'
        ];
    }
}
