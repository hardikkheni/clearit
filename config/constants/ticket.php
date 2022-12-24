<?php

return [
    'DEFAULT_TEXT_HEX_COLOR' => 'FFFFFF',
    'DEFAULT_HEX_COLOR' => '000000',
    'type' => [
        'ALL' => 'all',
        'CLEARANCE' => 'clearance',
        'CAR' => 'car',
        'AES' => 'aes',
        'FREIGHT' => 'freight',
        'CUSTOM' => 'custom',
    ],
    'status' => [
        'ALL' => 'all',
        'NEW' => 'New',
        'PROGRESS' => 'In Progress',
        'ISF' => 'ISF',
        'COMPLETE' => 'Complete',
        'TO_QUOTE' => 'Pending',
        'QUOTED' => 'Ready',
        'PAID' => 'Paid',
        'NOT_PAID' => 'Not paid',
        'MISC' => 'Misc',
        'OPEN' => 'Open',
    ],
    'transport' => [
        'TRUCK' => 1,
        'OCEAN' => 2,
        'AIR' => 3,
        'COURIER' => 4,
        'HANDCARRY' => 5,
    ],
    'ticketTypes' => [
        'clearance',
        'car',
        'freight',
        'custom',
    ],
    'transportModes' => [
        1, 2, 3, 4, 5
    ],
    'transportModesRequiresDelivery' => [2, 3],
    'NEW_TICKET_EMAIL' => 'new_ticket_email',
];
