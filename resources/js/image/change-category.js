const Logger = require('../common/Logger');
const CategoryChanger = require('./CategoryChanger');

document.addEventListener('DOMContentLoaded', () => {

    const logger = new Logger();

    try {
        new CategoryChanger(logger);
    } catch (err) {
        logger.exception(err);
    }

});
