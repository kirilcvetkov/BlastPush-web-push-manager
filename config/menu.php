<?php
return [
    'home' => [
        'route' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => 'mdi mdi-view-dashboard',
        'role' => 'any',
    ],
    'websites' => [
        'route' => 'websites.index',
        'title' => 'Websites',
        'icon' => 'mdi mdi-web',
        'role' => 'any',
    ],
    'campaigns' => [
        'route' => 'campaigns.index',
        'title' => 'Campaigns',
        'icon' => 'mdi mdi-group',
        'role' => 'any',
    ],
    'messages' => [
        'route' => 'messages.index',
        'title' => 'Messages',
        'icon' => 'mdi mdi-message-draw',
        'role' => 'any',
    ],
    'reports' => [
        'route' => 'reports.campaigns',
        'title' => 'Reports',
        'icon' => 'mdi mdi-chart-areaspline',
        'role' => 'any',
        'submenu' => [
            'reports.campaigns' => [
                'route' => 'reports.campaigns',
                'title' => 'Campaign Report',
                'icon' => 'mdi mdi-chart-areaspline',
                'role' => 'any',
            ],
            'reports.websites' => [
                'route' => 'reports.websites',
                'title' => 'Website Report',
                'icon' => 'mdi mdi-chart-areaspline',
                'role' => 'any',
            ],
        ]
    ],
    'variables' => [
        'route' => 'variables.index',
        'title' => 'Variables',
        'icon' => 'mdi mdi-code-braces',
        'role' => 'any',
    ],
    'subscribers' => [
        'route' => 'subscribers.index',
        'title' => 'Subscribers',
        'icon' => 'mdi mdi-account-group',
        'role' => 'any',
    ],
    'events' => [
        'route' => 'events.index',
        'title' => 'Events',
        'icon' => 'mdi mdi-calendar-alert',
        'role' => 'any',
    ],
    'push' => [
        'route' => 'push.index',
        'title' => 'Push Notifications',
        'icon' => 'mdi mdi-send',
        'role' => 'any',
    ],
    'users' => [
        'route' => 'users.index',
        'title' => 'Users',
        'icon' => 'mdi mdi-account-multiple-check',
        'role' => 'superuser',
    ],
    'roles' => [
        'route' => 'roles.index',
        'title' => 'Roles',
        'icon' => 'mdi mdi-account-box-multiple',
        'role' => 'superuser',
    ],
    'plans' => [
        'route' => 'plans.index',
        'title' => 'Plans',
        'icon' => 'mdi mdi-ballot',
        'role' => 'superuser',
    ],
    'logs-viewer' => [
        'route' => 'logs-viewer',
        'title' => 'Logs',
        'icon' => 'mdi mdi-format-list-bulleted',
        'role' => 'superuser',
    ],

    // Non-menu items
    'account' => [
        'route' => 'account.show',
        'title' => 'Account',
        'icon' => 'mdi mdi-shield-account',
        'role' => 'any',
        'nonmenu' => true,
    ],
    'profile.edit' => [
        'route' => 'profile.edit',
        'title' => 'Profile Edit',
        'icon' => 'mdi mdi-shield-account',
        'role' => 'any',
        'nonmenu' => true,
    ],
];
