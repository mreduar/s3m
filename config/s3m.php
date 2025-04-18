<?php

return [
    /**
     * Indicates whether the bucket of the uploaded file can be changed.
     * The default bucket is the one configured below.
     */
    'allow_change_bucket' => true,

    /**
     * Indicates whether the visibility of the uploaded file can be modified.
     * The default visibility setting is private.
     */
    'allow_change_visibility' => true,

    /**
     * Indicates whether the folder of the uploaded file can be changed.
     * By default, files are stored in the /tmp/ directory at the root of the bucket,
     * following the format /tmp/{filename}, where {filename} is the UUID generated for the upload.
     */
    'allow_change_folder' => false,

    /**
     * Middleware to be used for the multipart upload.  
     */
    'middleware' => [
        'web'
    ],

    /**
     * S3 configuration.
     */
    's3' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'token' => env('AWS_SESSION_TOKEN'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
];
