const Logger = require('../common/Logger');
const Uploader = require('./Uploader');

document.addEventListener('DOMContentLoaded', () => {

    const logger = new Logger();

    try {
        new Uploader(logger);
    } catch (err) {
        logger.exception(err);
    }

});