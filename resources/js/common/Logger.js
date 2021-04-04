const debug = require('./debug');

module.exports = class Logger {

    console(message) {

        debug(message);

    }

    exception(e) {

        debug(`${e.name}: ${e.message}`);

    }

};
