const DomTree = require('../common/DomTree');
const ResponseException = require('../exceptions/ResponseException');

/**
 * Приложение для изменения основного цвета для изображения.
 */
module.exports = class MainColorChanger {

    /**
     * @param logger
     */
    constructor(logger) {

        const self = this;

        /**
         * Для нужд логирования.
         *
         * @private
         */
        self._logger = logger;

        self._controlInit();

    }

    /**
     * Инициализация управления приложением.
     *
     * @private
     */
    _controlInit() {

        const self = this;
        const $additionalColors = self._getAdditionalColors();

        $additionalColors.forEach($_colorElement => {

            $_colorElement.addEventListener('click', e => {

                try {
                    self._selectColor(e, $_colorElement);
                } catch (err) {
                    self._logger.exception(err);
                }

            });

        });

    }

    /**
     * Клик по дополнительному цвету (из палитры выбран цвет, который нужно сделать основным).
     *
     * @param {Event} e
     * @param {Element} $colorElement
     * @private
     */
    _selectColor(e, $colorElement) {

        e.preventDefault();

        const self = this;

        // Основной элемент с изображением.
        const $image = $colorElement.closest('.category-image');

        const colorValue = window.
            getComputedStyle($colorElement, null).
            getPropertyValue('background-color');

        const imageId = self._getImageId($image);
        const $mainColor = self._getMainColor($image);

        self._mainColorSetSpin($mainColor, true);

        // Отправим на сервак запрос на изменение основного цвета.
        self._changeMainColor(imageId, colorValue, () => {

            // Изменим текущий основной цвет на новое значение.
            $mainColor.style.backgroundColor = colorValue;
            self._mainColorSetSpin($mainColor, false);

        });

    }

    /**
     * Оправляет на сервер запрос на изменение основного цвета.
     *
     * @param {number} imageId
     * @param {string} colorValue
     * @param {Function} callback
     * @private
     */
    _changeMainColor(imageId, colorValue, callback) {

        const self = this;
        const postUrl = self._getChangeMainColorUrl();

        axios.post(postUrl, {
            imageId,
            colorValue,
        }).then(response => {
            callback(response);
        }).catch(err => {

            self._logger.exception(
                new ResponseException(err),
            );

        });

    }

    /**
     * Возвращает коллекцию DOM-элементов для каждого дополнительного цвета на странице.
     *
     * @return {NodeListOf<Element>}
     * @private
     */
    _getAdditionalColors() {

        return DomTree.getAllObjectsBySelector(
            '.additional-color',
            'an error occurred while retrieving the additional color objects',
        );

    }

    /**
     * Возвращает ID изображения, для которого нужно изменить основной цвет.
     *
     * @param {Element} $image
     * @return {string}
     * @private
     */
    _getImageId($image) {

        return DomTree.getValueBySelector(
            'input[name=_image_id]',
            'an error occurred while retrieving the list URL value',
            $image,
        );

    }

    /**
     * Возвращает объект с основным цветом.
     *
     * @param {Element} $image
     * @return {Element}
     * @private
     */
    _getMainColor($image) {

        return DomTree.getObjectBySelector(
            '.main-color',
            'an error occurred while retrieving the main color object',
            $image,
        );

    }

    /**
     * Возвращает URL для выполнения замены основного цвета.
     *
     * @return {string}
     * @private
     */
    _getChangeMainColorUrl() {

        return DomTree.getValueBySelector(
            'input[name=_change_main_color_url]',
            'an error occurred while retrieving the list URL value',
        );

    }

    /**
     * Включает/выключает анимацию вращения на основном цвете.
     *
     * @param {Element} $color
     * @param {boolean} enabled
     * @private
     */
    _mainColorSetSpin($color, enabled) {

        if (enabled) {
            $color.classList.add('animate-spin');
        } else {
            $color.classList.remove('animate-spin');
        }
    }
};