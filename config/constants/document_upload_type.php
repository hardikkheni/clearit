<?php

return [
    'type' => [
        'COMMERCIAL_INVOICE' => 'Commercial Invoice',
        'BILL_OF_LADING' => 'Bill of lading',
        'ISF' => 'ISF FILE',
        'PAPS' => 'PAPS',
        'OTHER' => 'Other',
        '7501' => '7501',
        'ACE_CARGO_RELEASE' => 'ACE',
        'ISF_CERTIFICATE' => 'ISF Certificate',
    ],
    'mappedDocumentTypes' => [
        '7501' => 'US_CUSTOMS_INVOICE',
        'ISF Certificate' => 'ACE',
    ]
];
