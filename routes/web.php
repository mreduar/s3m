<?php

use Illuminate\Support\Facades\Route;
use MrEduar\S3M\Http\Controllers\S3MultipartController;

Route::get('s3m/create-multipart-upload', [S3MultipartController::class, 'createMultipartUpload'])->name('s3m.create-multipart');

Route::get('s3m/create-sign-part', [S3MultipartController::class, 'signPartUpload'])->name('s3m.create-sign-part');

Route::post('s3m/complete-multipart-upload', [S3MultipartController::class, 'completeMultipartUpload'])->name('s3m.complete-multipart');
