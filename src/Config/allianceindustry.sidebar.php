<?php
return [
    'allianceindustry' => [
        'name'          => 'Alliance Industry Planner',
        'icon'          => 'fas fa-industry',
        'route_segment' => 'allianceindustry',
        'permission' => 'allianceindustry.industrialist',
        'entries'       => [
            [
                'name'  => 'By Character',
                'icon'  => 'fas fa-user',
                'route' => 'rattingmonitor.character',
                'permission' => 'rattingmonitor.cat',
            ],
        ]
    ]
];