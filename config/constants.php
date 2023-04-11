<?php
$constants = [
    "eventTypesDetails" => [
        0 => [
            'name' => 'Unknown',
            'code' => 'unknown',
            'icon' => 'mdi mdi-comment-question',
            'color' => 'text-warning',
        ],
        1 => [
            'name' => 'Subscribe',
            'code' => 'subscribe',
            'icon' => 'mdi mdi-account-plus',
            'color' => 'text-success',
        ],
        2 => [
            'name' => 'Unsubscribe',
            'code' => 'unsubscribe',
            'icon' => 'mdi mdi-account-minus',
            'color' => 'text-danger',
        ],
        3 => [
            'name' => 'Visit',
            'short' => 'Visit',
            'code' => 'visit',
            'icon' => 'mdi mdi-account-check',
            'color' => 'text-info',
        ],
        // 4 => [
        //     'name' => 'Dialog Box Show',
        //     'code' => 'dialog',
        //     'icon' => 'mdi mdi-window-maximize',
        //     'color' => 'text-info',
        // ],
        5 => [
            'name' => 'Notification Delivered',
            'short' => 'Delivered',
            'code' => 'notification-delivered',
            'icon' => 'mdi mdi-comment-text',
            'color' => 'text-success',
        ],
        6 => [
            'name' => 'Notification Clicked',
            'short' => 'Clicked',
            'code' => 'notification-clicked',
            'icon' => 'mdi mdi-comment-plus',
            'color' => 'text-primary',
        ],
        7 => [
            'name' => 'Notification Closed',
            'short' => 'Closed',
            'code' => 'notification-closed',
            'icon' => 'mdi mdi-comment-remove', //-outline
            'color' => 'text-warning',
        ],
        8 => [
            'name' => 'Notification TTL Expired OnPush Payload',
            'code' => 'notification-ttl-expired-onpush-payload',
            'icon' => 'mdi mdi-timer-off',
            'color' => 'text-warning',
        ],
        9 => [
            'name' => 'Subscription Error',
            'code' => 'subscription-error',
            'icon' => 'mdi mdi-account-remove',
            'color' => 'text-danger',
        ],
        10 => [
            'name' => 'Permission Denied',
            'short' => 'Denied',
            'code' => 'permission-denied',
            'icon' => 'mdi mdi-account-off',
            'color' => 'text-danger',
        ],
        11 => [
            'name' => 'Service Worker Error - Subscription Retrieval',
            'code' => 'sw-error-subscription-retrieval',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        12 => [
            'name' => 'Service Worker Error - Payload Retrieval',
            'code' => 'sw-error-payload-retrieval',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        13 => [
            'name' => 'Service Worker Error - No Payload',
            'code' => 'sw-error-no-payload',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        14 => [
            'name' => 'Service Worker Error - Delivery Tracking',
            'code' => 'sw-error-delivery-tracking',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        15 => [
            'name' => 'Service Worker Error - Show Notification',
            'code' => 'sw-error-show-notification',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        16 => [
            'name' => 'Service Worker Error - Subscription Changed',
            'code' => 'sw-error-subscription-changed',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        17 => [
            'name' => 'Service Worker - Local DB Failed',
            'code' => 'swl-localforage-failed',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        18 => [
            'name' => 'Service Worker Error - No Click Data',
            'code' => 'sw-error-no-click-data',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        19 => [
            'name' => 'Service Worker Error - Generic SW',
            'code' => 'sw-error-generic',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        20 => [
            'name' => 'Service Worker Error - Generic Web Storage',
            'code' => 'webpush-error-storage',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
        21 => [
            'name' => 'Service Worker Error - Generic Web',
            'code' => 'webpush-error-generic',
            'icon' => 'mdi mdi-close-octagon',
            'color' => 'text-danger',
        ],
    ],
    'webhookMethod' => [
        'get' => 0,
        'post' => 1,
    ],
    'webhookResponseStatus' => [
        'fail' => 0,
        'success' => 1,
        'queue' => 2,
        'queue-retry' => 3,
    ],
    'webhookResponseStatusNames' => [
        0 => "Fail",
        1 => "Success",
        2 => "Queue",
        3 => "Fail, retry",
    ],
    'webhookResponseStatusIcon' => [
        0 => "mdi mdi-close-circle-outline",
        1 => "mdi mdi-checkbox-marked-circle-outline",
        2 => "mdi mdi-play-circle-outline",
        3 => "mdi mdi-clock",
    ],
    'webhookResponseStatusColor' => [
        0 => "danger",
        1 => "success",
        2 => "info",
        3 => "warning",
    ],
    'queueResponseStatus' => [
        'fail' => 0,
        'success' => 1,
        'queue' => 2,
        'queue-retry' => 3,
    ],
    'queueResponseStatusNames' => [
        0 => "Fail",
        1 => "Success",
        2 => "Queue",
        3 => "Fail, retry",
    ],
    'queueResponseStatusIcon' => [
        0 => "mdi mdi-close-circle-outline",
        1 => "mdi mdi-checkbox-marked-circle-outline",
        2 => "mdi mdi-clock-outline",
        3 => "mdi mdi-clock-alert",
    ],
    'queueResponseStatusColor' => [
        0 => "danger",
        1 => "success",
        2 => "info",
        3 => "warning",
    ],
    'campaignReoccurringFrequency' => [
        "hourly",
        "daily",
        "weekly",
        "monthly",
    ],
    'campaignReoccurringFrequencyColors' => [
        "hourly" => "primary",
        "daily" => "success",
        "weekly" => "info",
        "monthly" => "danger",
    ],
    'campaignWeekdays' => [
        0 => "Monday",
        1 => "Tuesday",
        2 => "Wednesday",
        3 => "Thursday",
        4 => "Friday",
        5 => "Saturday",
        6 => "Sunday",
    ],
];

$constants["eventTypes"] = array_combine(
    array_keys($constants["eventTypesDetails"]),
    array_column($constants["eventTypesDetails"], 'code')
);

$constants["eventTypesArr"] = array_flip($constants["eventTypes"]);

$constants["direction"] = ['auto', 'ltr', 'rtl'];

$constants["jsVersion"] = 0.39;

$constants['plansPushLimitTimeframe'] = [
    0 => 'unlimited',
    1 => 'daily',
    2 => 'weekly',
    3 => 'monthly',
    4 => 'yearly',
];

$constants['campaignTypes'] = [
    'waterfall', // 0
    'scheduled', // 1
    'reoccurring', // 2
];

$constants['variableScopes'] = [
    'global',
    'website',
    'campaign',
    'schedule',
    'subscriber',
    // 'message',
    // 'push',
];

$constants['variableScopesColors'] = [
    'global' => 'info',
    'website' => 'success',
    'subscriber' => 'info',
    'campaign' => 'danger',
    'schedule' => 'primary',
    'push' => 'dark',
];

$constants['variableColumns'] = [
    'global' => [], // all custom
    'website' => [
        'id',
        'name',
        'domain',
    ],
    'campaign' => [
        'id',
        'name',
        'type',
    ],
    'schedule' => [
        'id',
        'delay',
        'scheduled_at',
        'reoccurring_frequency',
        'hour_minute',
        'day',
        'is_trigger',
        'trigger_schedule_id',
        'message_id',
        'order',
    ],
    'subscriber' => [
        'id',
        'ip',
        'user_agent',
        'device',
        'platform',
        'browser',
        'is_mobile',
        'is_tablet',
        'is_desktop',
        'is_robot',
        'referer',
    ],
    // 'message' => [
    //     'id',
    //     'title',
    //     'button',
    // ],
    'push' => [
        'uuid',
        'sent_at',
    ],
];

return $constants;
