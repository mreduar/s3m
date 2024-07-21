<?php

use Illuminate\Support\Facades\Route;
use MrEduar\LaravelS3Multipart\Http\Controllers\S3MultipartController;

Route::get('s3m/create-multipart-upload', [S3MultipartController::class, 'createMultipartUpload'])->name('s3m.create-multipart');
