export default class S3M {
    /**
     * Default chunk size in bytes for the upload.
     *
     * @type {number}
     */
    static DEFAULT_CHUNK_SIZE = 10 * 1024 * 1024; // mb * kb * b = 10mb

    /**
     * Maximum number of concurrent uploads.
     *
     * @type {number}
     */
    static DEFAULT_MAX_CONCURRENT_UPLOADS = 5;

    /**
     * Maximum number of retries for a failed upload.
     *
     * @type {number}
     */
    static DEFAULT_MAX_CHUNK_RETRIES = 3;

    /**
     * Creates a new S3M instance.
     *
     * @param {File} [file] - File to upload.
     * @param {Object} [options] - Options for the upload.
     */
    constructor(file, options = {}) {
        this.file = file;

        this.options = options;

        this.chunkSize = options.chunk_size || S3M.DEFAULT_CHUNK_SIZE;

        this.maxConcurrentUploads =
            options.max_concurrent_uploads || S3M.DEFAULT_MAX_CONCURRENT_UPLOADS;

        this.fileName = file.name;

        this.fileSize = file.size;

        this.fileType = file.type;

        this.httpClient = options.httpClient ? options.httpClient : axios;

        this.chunkRetries = options.chunk_retries || S3M.DEFAULT_MAX_CHUNK_RETRIES;
    }

    /**
     * Starts the upload.
     *
     * @returns {Promise<{key: string, uploadId: string, uuid: string}>}
     */
    async startUpload() {
        if (!this.fileName) {
            throw new Error('Filename is empty');
        }

        const { data } = await this.httpClient.post(
            '/s3m/create-multipart-upload',
            {
                filename: this.fileName,
                content_type: this.fileType,
                ...this.options.data,
            },
            {
                baseURL: this.options.baseURL || null,
                headers: this.options.headers || {},
                ...this.options.options,
            },
        );

        return data;
    }

    /**
     * Uploads the file to S3.
     *
     * @returns {Promise<{uuid: string, key: string, extension: string, name: string, url: string}>}
     */
    async upload() {
        try {
            const { key, uploadId, uuid } = await this.startUpload();

            if (!uploadId) {
                console.error('Upload ID not found');
                return;
            }

            const updateProgress = this.options.progress || (() => {});

            const uploadParts = await this.uploadChunks(key, uploadId, updateProgress);

            if (this.options.auto_complete === false) {
                updateProgress(100);

                return {
                    uuid,
                    key,
                    extension: this.fileName.split('.').pop(),
                    name: this.fileName,
                    upload_id: uploadId,
                    parts: uploadParts,
                };
            }

            const fileUrl = await this.completeUpload(key, uploadId, uploadParts);

            updateProgress(100);

            return {
                uuid,
                key,
                extension: this.fileName.split('.').pop(),
                name: this.fileName,
                url: fileUrl,
            };
        } catch (error) {
            console.error(error);
        }
    }

    /**
     * Uploads the file in chunks.
     *
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {Function} updateProgress - Function to update the upload progress.
     * @returns
     */
    async uploadChunks(key, uploadId, updateProgress) {
        const totalChunks = Math.ceil(this.fileSize / this.chunkSize);
        const progress = new Array(totalChunks).fill(0);
        const parts = [];
        let activeUploads = 0;
        let currentChunk = 0;

        const uploadNextChunk = async () => {
            if (currentChunk >= totalChunks) return;

            const start = currentChunk * this.chunkSize;
            const end = Math.min(start + this.chunkSize, this.fileSize);
            const chunk = this.file.slice(start, end);

            activeUploads++;
            currentChunk++;

            const partNumber = currentChunk;

            const part = await this.uploadChunk(
                key,
                uploadId,
                partNumber,
                chunk,
                totalChunks,
                progress,
                updateProgress,
            );
            parts.push(part);

            activeUploads--;

            if (activeUploads < this.maxConcurrentUploads) {
                uploadNextChunk();
            }
        };

        const initialUploads = Array.from({
            length: this.maxConcurrentUploads,
        }).map(uploadNextChunk);

        await Promise.all(initialUploads);

        while (activeUploads > 0) {
            await new Promise((resolve) => setTimeout(resolve, 100));
        }

        return parts.sort((a, b) => a.PartNumber - b.PartNumber);
    }

    /**
     * Completes the upload.
     *
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {Array} parts - Parts of the file.
     */
    async completeUpload(key, uploadId, parts) {
        const {
            data: { url },
        } = await this.httpClient.post(
            '/s3m/complete-multipart-upload',
            {
                parts,
                upload_id: uploadId,
                key,
            },
            {
                baseURL: this.options.baseURL || null,
                headers: this.options.headers || {},
                ...this.options.options,
            },
        );

        return url;
    }

    /**
     * Gets the signed URL for the part.
     *
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {number} partNumber - Part number.
     * @returns {Promise<string>}
     */
    async getSignUrl(key, uploadId, partNumber) {
        const {
            data: { url },
        } = await this.httpClient.post(
            '/s3m/create-sign-part',
            {
                filename: this.fileName,
                content_type: this.fileType,
                part_number: partNumber,
                upload_id: uploadId,
                key,
                ...this.options.data,
            },
            {
                baseURL: this.options.baseURL || null,
                headers: this.options.headers || {},
                ...this.options.options,
            },
        );

        return url;
    }

    /**
     * Uploads a chunk of the file.
     *
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {number} partNumber - Part number.
     * @param {Blob} chunk - Chunk of the file.
     * @param {number} totalChunks - Total number of chunks.
     * @param {Array} progress - Progress of each chunk.
     * @param {Function} updateProgress - Function to update the upload progress.
     * @returns {Promise<{ETag: string, PartNumber: number}>}
     *
     * @see https://docs.aws.amazon.com/AmazonS3/latest/API/API_UploadPart.html
     */
    async uploadChunk(key, uploadId, partNumber, chunk, totalChunks, progress, updateProgress) {
        const url = await this.getSignUrl(key, uploadId, partNumber);

        const attemptUpload = async (retryCount = 0) => {
            try {
                const response = await this.httpClient.put(url, chunk, {
                    headers: { 'Content-Type': this.fileType },
                    onUploadProgress: (event) =>
                        this.handleUploadProgress(
                            event,
                            totalChunks,
                            partNumber - 1,
                            progress,
                            updateProgress,
                        ),
                });

                return {
                    ETag: response.headers.etag,
                    PartNumber: partNumber,
                };
            } catch (error) {
                if (retryCount < this.chunkRetries) {
                    console.warn(`Retrying chunk ${partNumber}, attempt ${retryCount + 1}`);

                    return attemptUpload(retryCount + 1);
                } else {
                    throw error;
                }
            }
        };

        return attemptUpload();
    }

    /**
     * Handles the upload progress.
     *
     * @param {ProgressEvent} event - Progress event.
     * @param {number} totalChunks - Total number of chunks.
     * @param {number} chunkIndex - Index of the chunk.
     * @param {Array} progress - Progress of each chunk.
     * @param {Function} updateProgress - Function to update the upload progress.
     * @returns {
     *    void
     * }
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/ProgressEvent
     */
    handleUploadProgress(event, totalChunks, chunkIndex, progress, updateProgress) {
        const currentProgress = Math.round((event.loaded * 100) / event.total);

        progress[chunkIndex] = currentProgress;

        const overallProgress = Math.round(
            progress.reduce((acc, curr) => acc + curr) / totalChunks,
        );

        updateProgress(overallProgress);
    }
}
