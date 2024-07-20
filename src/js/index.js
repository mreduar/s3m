import S3M from './S3M.js';

export function s3m(file, options) {
    const s3M = new S3M(file, options);

    return s3M.upload();
}
