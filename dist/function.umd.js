!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e||self).s3m=t()}(this,function(){function e(){return e=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)({}).hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},e.apply(null,arguments)}var t=/*#__PURE__*/function(){function t(e,n){void 0===n&&(n={}),this.file=e,this.options=n,this.chunkSize=n.chunk_size||t.DEFAULT_CHUNK_SIZE,this.maxConcurrentUploads=n.max_concurrent_uploads||t.DEFAULT_MAX_CONCURRENT_UPLOADS,this.fileName=e.name,this.fileSize=e.size,this.fileType=e.type,this.httpClient=n.httpClient?n.httpClient:axios}var i=t.prototype;return i.startUpload=function(){try{var t=this;if(!t.fileName)throw new Error("Filename is empty");return Promise.resolve(t.httpClient.post("/s3m/create-multipart-upload",e({filename:t.fileName,content_type:t.fileType},t.options.data),e({baseURL:t.options.baseURL||null,headers:t.options.headers||{}},t.options.options))).then(function(e){return e.data})}catch(e){return Promise.reject(e)}},i.upload=function(){try{var e=this;return Promise.resolve(function(t,n){try{var r=Promise.resolve(e.startUpload(e.file)).then(function(t){var n=t.key,r=t.uploadId,o=t.uuid;if(r){var i=e.options.progress||function(){};return Promise.resolve(e.uploadChunks(n,r,i)).then(function(t){return Promise.resolve(e.completeUpload(n,r,t)).then(function(t){return i(100),{uuid:o,key:n,extension:e.fileName.split(".").pop(),name:e.fileName,url:t}})})}console.error("Upload ID not found")})}catch(e){return n(e)}return r&&r.then?r.then(void 0,n):r}(0,function(e){console.error(e)}))}catch(e){return Promise.reject(e)}},i.uploadChunks=function(e,t,i){try{var u=this,s=Math.ceil(u.fileSize/u.chunkSize),a=new Array(s).fill(0),l=[],h=0,c=0,f=Array.from({length:u.maxConcurrentUploads}).map(function n(){try{if(c>=s)return Promise.resolve();var r=c*u.chunkSize,o=Math.min(r+u.chunkSize,u.fileSize),f=u.file.slice(r,o);return h++,c++,Promise.resolve(u.uploadChunk(e,t,c,f,s,a,i)).then(function(e){l.push(e),--h<u.maxConcurrentUploads&&n()})}catch(e){return Promise.reject(e)}});return Promise.resolve(Promise.all(f)).then(function(){function e(){return l.sort(function(e,t){return e.PartNumber-t.PartNumber})}var t=function(e,t,i){for(var u;;){var s=e();if(o(s)&&(s=s.v),!s)return a;if(s.then){u=0;break}var a=i();if(a&&a.then){if(!o(a)){u=1;break}a=a.s}if(t){var l=t();if(l&&l.then&&!o(l)){u=2;break}}}var h=new r,c=n.bind(null,h,2);return(0===u?s.then(p):1===u?a.then(f):l.then(d)).then(void 0,c),h;function f(r){a=r;do{if(t&&(l=t())&&l.then&&!o(l))return void l.then(d).then(void 0,c);if(!(s=e())||o(s)&&!s.v)return void n(h,1,a);if(s.then)return void s.then(p).then(void 0,c);o(a=i())&&(a=a.v)}while(!a||!a.then);a.then(f).then(void 0,c)}function p(e){e?(a=i())&&a.then?a.then(f).then(void 0,c):f(a):n(h,1,a)}function d(){(s=e())?s.then?s.then(p).then(void 0,c):p(s):n(h,1,a)}}(function(){return h>0},void 0,function(){return Promise.resolve(new Promise(function(e){return setTimeout(e,100)})).then(function(){})});return t&&t.then?t.then(e):e()})}catch(e){return Promise.reject(e)}},i.completeUpload=function(t,n,r){try{var o=this;return Promise.resolve(o.httpClient.post("/s3m/complete-multipart-upload",{parts:r,upload_id:n,key:t},e({baseURL:o.options.baseURL||null,headers:o.options.headers||{}},o.options.options))).then(function(e){return e.data.url})}catch(e){return Promise.reject(e)}},i.getSignUrl=function(t,n,r){try{var o=this;return Promise.resolve(o.httpClient.post("/s3m/create-sign-part",e({filename:o.fileName,content_type:o.fileType,part_number:r,upload_id:n,key:t},o.options.data),e({baseURL:o.options.baseURL||null,headers:o.options.headers||{}},o.options.options))).then(function(e){return e.data.url})}catch(e){return Promise.reject(e)}},i.uploadChunk=function(e,t,n,r,o,i,u){try{var s=this;return Promise.resolve(s.getSignUrl(e,t,n)).then(function(e){return Promise.resolve(s.httpClient.put(e,r,{headers:{"Content-Type":s.fileType},onUploadProgress:function(e){return s.handleUploadProgress(e,o,n-1,i,u)}})).then(function(e){return{ETag:e.headers.etag,PartNumber:n}})})}catch(e){return Promise.reject(e)}},i.handleUploadProgress=function(e,t,n,r,o){var i=Math.round(100*e.loaded/e.total);r[n]=i,o(Math.round(r.reduce(function(e,t){return e+t})/t))},t}();function n(e,t,o){if(!e.s){if(o instanceof r){if(!o.s)return void(o.o=n.bind(null,e,t));1&t&&(t=o.s),o=o.v}if(o&&o.then)return void o.then(n.bind(null,e,t),n.bind(null,e,2));e.s=t,e.v=o;const i=e.o;i&&i(e)}}var r=/*#__PURE__*/function(){function e(){}return e.prototype.then=function(t,r){var o=new e,i=this.s;if(i){var u=1&i?t:r;if(u){try{n(o,1,u(this.v))}catch(e){n(o,2,e)}return o}return this}return this.o=function(e){try{var i=e.v;1&e.s?n(o,1,t?t(i):i):r?n(o,1,r(i)):n(o,2,i)}catch(e){n(o,2,e)}},o},e}();function o(e){return e instanceof r&&1&e.s}return t.DEFAULT_CHUNK_SIZE=10485760,t.DEFAULT_MAX_CONCURRENT_UPLOADS=5,function(e,n){return new t(e,n).upload()}});
