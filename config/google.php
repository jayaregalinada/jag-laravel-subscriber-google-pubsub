<?php

return [
    'driver' => 'google',
    'project_id' => env('GOOGLE_PUBSUB_SUBSCRIBER_PROJECT_ID', env('GOOGLE_PROJECT_ID', env('GCLOUD_PROJECT'))),
    'credentials_path' => env('GOOGLE_PUBSUB_SUBSCRIBER_CREDENTIALS', env('GOOGLE_APPLICATION_CREDENTIALS')),
    'max_messages' => env('GOOGLE_PUBSUB_SUBSCRIBER_MAX_MESSAGE', 1000),
    'return_immediately' => env('GOOGLE_PUBSUB_SUBSCRIBER_RETURN_IMMEDIATELY', false),
    'override_config' => [],
];
