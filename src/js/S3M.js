export default class S3M {
    /**
     * @param {File} [file] - File to upload.
     * @param {Object} [options] - Options for the upload.
     */
    constructor(file, options = {}) {
        this.file = file;
        this.options = options;
    }

    async upload() {
        console.log('Uploading file: ', this.file);
    }
}
