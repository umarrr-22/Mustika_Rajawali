<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "public" disk is best for web-accessible files.
    */
    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => true,
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => true,
            'permissions' => [
                'file' => [
                    'public' => 0664,  // Read/write for owner/group, read for others
                    'private' => 0600, // Read/write for owner only
                ],
                'dir' => [
                    'public' => 0775,  // Read/write/search for owner/group, read/search for others
                    'private' => 0700, // Read/write/search for owner only
                ],
            ],
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    */
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Configuration for Service Images
    |--------------------------------------------------------------------------
    |
    | Special configuration for service image uploads including directory,
    | size limits, allowed types, and thumbnail settings.
    */
    'service_images' => [
        'directory' => 'service_images',  // Subdirectory under public disk
        'max_size' => 2048,              // Maximum file size in KB (2MB)
        'allowed_mimes' => [             // Allowed file types
            'jpeg',
            'png',
            'jpg',
            'webp'
        ],
        'thumbnail' => [                 // Thumbnail generation settings
            'width' => 150,              // Thumbnail width in pixels
            'height' => 150,             // Thumbnail height in pixels
            'quality' => 80              // Thumbnail quality percentage
        ],
        'optimize' => true               // Whether to optimize images on upload
    ],
];