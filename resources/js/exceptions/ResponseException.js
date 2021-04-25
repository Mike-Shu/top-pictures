/**
 * @param {Object} error
 */
module.exports = function ResponseException(error) {

    this.name = 'ResponseException';
    this.message = `${error.message} (${error.response.status}): ${error.config.url}`;

};
