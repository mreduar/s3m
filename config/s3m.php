<?php

return [
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
];
