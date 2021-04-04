/**
 * Response exception.
 *
 * @param response
 */
module.exports = function ResponseException(response) {

    this.name = 'ResponseException';
    this.message = `${response.statusText} (${response.status}): ${response.url}`;

};
