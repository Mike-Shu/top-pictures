const debug = require('../common/debug');
const DomTree = require('../common/DomTree');
const ResponseException = require('../exceptions/ResponseException');

/**
 * Приложение для замены категории у изображения.
 */
module.exports = class CategoryChanger {

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

        // Добавляем на страницу список актуальных категорий.
        self._fetchCategoriesList(($list) => {
            self._renderCategoriesList($list);
            self._bindCategoriesList();
        });

    }

    /**
     * Запрашивает с сервера список актуальных категорий.
     *
     * @private
     */
    _fetchCategoriesList(callback) {

        const self = this;
        const getUrl = self._getCategoriesListUrl();

        axios.get(getUrl).
            then(response => {
                callback(response.data);
            }).
            catch(err => {

                self._logger.exception(
                    new ResponseException(err),
                );

            });

    }

    /**
     * Рендерит список категорий для каждой кнопки.
     *
     * @param {string} $list
     * @private
     */
    _renderCategoriesList($list) {

        const self = this;

        // Вытащим все DOM-элементы для списка категорий.
        const contentsList = self._getContentsList();

        // Нарисуем список категорий в каждом элементе.
        contentsList.forEach($_content => {
            $_content.innerHTML = $list;
        });

    }

    /**
     * Назначает обработчики для категорий в списке.
     *
     * @private
     */
    _bindCategoriesList() {

        const self = this;

        // Вытащим DOM-элементы для каждой отдельной категории в списке.
        const categoryElements = self._getCategoryElements();

        // Назначим обработчик для каждой категории.
        categoryElements.forEach($_categoryElement => {

            $_categoryElement.addEventListener('click', e => {

                try {
                    self._selectCategory(e, $_categoryElement);
                } catch (err) {
                    self._logger.exception(err);
                }

            });

        });

    }

    /**
     * Клик по категории (из списка выбрана категория, на которую нужно выполнить замену).
     *
     * @param {Event} e
     * @param {Element} $categoryElement
     * @private
     */
    _selectCategory(e, $categoryElement) {

        e.preventDefault();

        const self = this;

        // Основной элемент с изображением.
        const $image = $categoryElement.closest('.category-image');

        const imageId = DomTree.getValueBySelector(
            'input[name=_image_id]',
            'an error occurred while retrieving the list URL value',
            $image,
        );

        const categoryId = DomTree.getValueBySelector(
            'input[name=_category_id]',
            'an error occurred while retrieving the list URL value',
            $categoryElement,
        );

        // Кнопка "Сменить категорию".
        const $button = DomTree.getObjectBySelector(
            '.change-category-button',
            'an error occurred while retrieving the button object',
            $image,
        );

        // Переключим кнопку в режим "не доступна".
        self._buttonEnabled($button, false);

        // Инициируем смену категории для текущего изображения.
        self._changeCategory(imageId, categoryId, (response) => {
            $button.remove();
            $image.classList.add('filter', 'grayscale', 'blur');
            window.location.reload();
        });

    }

    /**
     * Сменить категорию! Выполняет запрос на сервер.
     *
     * @param {number}   imageId     Какое изображение?
     * @param {number}   categoryId  В какую категорию отправить?
     * @param {function} callback
     * @private
     */
    _changeCategory(imageId, categoryId, callback) {

        const self = this;
        const postUrl = self._getChangeCategoryUrl();

        axios.post(postUrl, {
            imageId,
            categoryId,
        }).then(response => {
            callback(response);
        }).catch(err => {

            self._logger.exception(
                new ResponseException(err),
            );

        });

    }

    /**
     * Возвращает все DOM-элементы, в которые нужно поместить список категорий.
     *
     * @return {NodeListOf<*>}
     * @private
     */
    _getContentsList() {

        return DomTree.getAllObjectsBySelector(
            '.change-category-content',
            'an error occurred while retrieving the content objects list',
        );

    }

    /**
     * Возвращает DOM-элементы для каждой отдельной категории в списке.
     *
     * @return {NodeListOf<*>}
     * @private
     */
    _getCategoryElements() {

        return DomTree.getAllObjectsBySelector(
            '.change-category-item',
            'an error occurred while retrieving the category items',
        );

    }

    /**
     * Возвращает URL для получения списка актуальных категорий.
     *
     * @return {string}
     * @private
     */
    _getCategoriesListUrl() {

        return DomTree.getValueBySelector(
            'input[name=_categories_list_url]',
            'an error occurred while retrieving the list URL value',
        );

    }

    /**
     * Возвращает URL для выполнения замены категории.
     *
     * @return {*}
     * @private
     */
    _getChangeCategoryUrl() {

        return DomTree.getValueBySelector(
            'input[name=_change_category_url]',
            'an error occurred while retrieving the list URL value',
        );

    }

    /**
     * Переключает статус кнопки "Сменить категорию": доступна/не доступна.
     *
     * @param {HTMLObjectElement} $button
     * @param {boolean} enabled
     * @private
     */
    _buttonEnabled($button, enabled) {

        const $svg = DomTree.getObjectBySelector(
            'svg',
            'an error occurred while retrieving the "SVG" object',
            $button,
        );

        $button.disabled = !enabled;

        if (enabled) {
            $svg.classList.remove('animate-spin');
            $svg.classList.add('transform', 'rotate-45');
        } else {
            $svg.classList.remove('transform', 'rotate-45');
            $svg.classList.add('animate-spin');
        }

    }
};