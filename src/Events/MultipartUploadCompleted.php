<?php

namespace MrEduar\S3M\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MultipartUploadCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $bucket,
        public string $key,
        public string $uploadId,
        public string $url,
    ) {}
}
