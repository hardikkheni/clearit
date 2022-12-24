<?php

namespace App\Http\Requests\Api\Helper\Affiliate;

use App\Http\Requests\Api\BaseFormRequest;

class EditAffiliateRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'companyname' => 'required|max:255',
            'affiliateCode' => 'required|max:255',
            'notificationEmail' => 'email|required|max:255',
            'logofilename' => 'file',
            'contactfirstname' => '',
            'contactlastname' => '',
            'phone' => '',
            'accent_color' => '',
            'expressEnabled' => 'in:true,false',
            'isPaymentProfileRequired' => 'in:true,false',
            'isCreditAccount' => 'in:true,false',
            'disableChatEmails' => 'in:true,false',
            'website' => '',
            'mail_list_id' => '',
            'poaVerbiage' => '',
            'poaCompanyInfo' => '',
            'poaThankyouUrl' => '',
            'isActive' => 'in:true,false',
            'remove_logo' => 'in:true,false',
        ];
    }
}
