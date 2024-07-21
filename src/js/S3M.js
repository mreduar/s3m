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
     * Creates a new S3M instance.
     *
     * @param {File} [file] - File to upload.
     * @param {Object} [options] - Options for the upload.
     */
    constructor(file, options = {}) {
        this.file = file;
        this.options = options;
    }

    /**
     * Starts the upload.
     *
     * @param {File} file
     * @returns {Promise<{key: string, uploadId: string, uuid: string}>}
     */
    async startUpload(file) {
        const { name: filename, type: contentType } = file;

        if (!filename) {
            throw new Error('Filename is empty');
        }

        const { data } = await axios.get('/s3m/create-multipart-upload', {
            params: { filename, content_type: contentType },
        });

        return data;
    }

    /**
     * Uploads the file to S3.
     *
     * @returns {Promise<{uuid: string, key: string, extension: string, name: string, url: string}>}
     */
    async upload() {
        try {
            const { key, uploadId, uuid } = await this.startUpload(this.file);

            if (!uploadId) {
                console.error('Upload ID not found');
                return;
            }

            const updateProgress = this.options.progress || (() => {});

            const uploadParts = await this.uploadChunks(this.file, key, uploadId, updateProgress);

            const fileUrl = await this.completeUpload(key, uploadId, uploadParts);

            updateProgress(100);

            return {
                uuid,
                key,
                extension: this.file.name.split('.').pop(),
                name: this.file.name,
                url: fileUrl,
            };
        } catch (error) {
            console.error(error);
        }
    }

    /**
     * Uploads the file in chunks.
     *
     * @param {File} file - File to upload.
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {Function} updateProgress - Function to update the upload progress.
     * @returns
     */
    async uploadChunks(file, key, uploadId, updateProgress) {
        const chunkSize = this.options.chunk_size || S3M.DEFAULT_CHUNK_SIZE;
        const maxConruentUploads =
            this.options.max_concurrent_uploads || S3M.DEFAULT_MAX_CONCURRENT_UPLOADS;
        const totalChunks = Math.ceil(file.size / chunkSize);
        const progress = new Array(totalChunks).fill(0);
        const parts = [];

        let activeUploads = 0;
        let currentChunk = 0;

        const uploadNextChunk = async () => {
            if (currentChunk >= totalChunks) return;

            const start = currentChunk * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            activeUploads++;
            currentChunk++;

            const partNumber = currentChunk;

            const part = await this.uploadChunk(
                file,
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

            if (activeUploads < maxConruentUploads) {
                uploadNextChunk();
            }
        };

        const initialUploads = Array.from({
            length: maxConruentUploads,
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
        } = await axios.post('/s3m/complete-multipart-upload', {
            parts,
            upload_id: uploadId,
            key,
        });

        return url;
    }

    /**
     * Gets the signed URL for the part.
     *
     * @param {File} file - File to upload.
     * @param {string} key - Key to store the file under.
     * @param {string} uploadId - Upload ID.
     * @param {number} partNumber - Part number.
     * @returns {Promise<string>}
     */
    async getSignUrl(file, key, uploadId, partNumber) {
        const {
            data: { url },
        } = await axios.get('/s3m/create-sign-part', {
            params: {
                filename: file.name,
                content_type: file.type,
                part_number: partNumber,
                upload_id: uploadId,
                key,
            },
        });

        return url;
    }

    /**
     * Uploads a chunk of the file.
     *
     * @param {File} file - File to upload.
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
    async uploadChunk(
        file,
        key,
        uploadId,
        partNumber,
        chunk,
        totalChunks,
        progress,
        updateProgress,
    ) {
        const url = await this.getSignUrl(file, key, uploadId, partNumber);

        const response = await axios.put(url, chunk, {
            headers: { 'Content-Type': file.type },
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
