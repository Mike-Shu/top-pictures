/**
 * Инкапсуляция console.log() для работы только на DEV'e.
 * Требуется для того, чтобы на PROD'е исключить отладочный вывод в консоль.
 *
 * @param value
 */
module.exports = function debug(value) {

    if (JSON.parse(process.env.MIX_APP_DEBUG)) {
        console.log(value);
    }

};
