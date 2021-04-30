const Logger = require('../common/Logger');
const MainColorChanger = require('./MainColorChanger');

document.addEventListener('DOMContentLoaded', () => {

    const logger = new Logger();

    try {
        new MainColorChanger(logger);
    } catch (err) {
        logger.exception(err);
    }

});
