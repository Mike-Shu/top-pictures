module.exports = class Slider {

    /**
     * Приложение для выполнения анимации "slide up" и "slide down".
     *
     * Пример использования:
     * ```
     * const Slider = require('../common/Slider');
     * const $myEle = document.querySelector('.my-element');
     *
     * Slider.
     *    speed(600).
     *    easing('ease-in-out').
     *    delay(100).
     *    up($myEle).
     *    then(() => {
     *        // Do something...
     *    });
     * ```
     *
     * @param fading Эффект "затухание" для анимации (fade in, fade out).
     */
    constructor(fading = false) {

        const self = this;

        self.EASE = 'ease';
        self.EASE_IN = 'ease-in';
        self.EASE_OUT = 'ease-out';
        self.EASE_IN_OUT = 'ease-in-out';
        self.LINEAR = "linear";

        /**
         * CSS-класс, описывающий скрытый DOM-элемент, для которого будет выполняться анимация.
         * Описывается в файле "custom.css".
         *
         * @type {string}
         * @private
         */
        self._HIDDEN_CLASS_NAME = 'slider-hidden';

        /**
         * Скорость выполнения анимации (мс).
         *
         * @type {number}
         * @private
         */
        self._speed = 300;

        /**
         * Функция, описывающая плавность выполнения анимации. Например:
         *  - ease        (default) Анимация начинается медленно, затем ускоряется и к концу движения опять замедляется.
         *  - ease-in     Анимация медленно начинается, к концу ускоряется.
         *  - ease-out    Анимация начинается быстро, к концу замедляется.
         *  - ease-in-out Анимация начинается и заканчивается медленно.
         *  - linear      Одинаковая скорость от начала и до конца.
         *
         * @type {string}
         * @see http://htmlbook.ru/css/transition-timing-function
         * @private
         */
        self._easing = 'ease';

        /**
         * Задержка перед выполнением анимации.
         *
         * @type {number}
         * @private
         */
        self._delay = 0;

        if (fading) {
            self._turnOnFading();
        }

    }

    /**
     * Показать элемент (slide down).
     *
     * @param element
     * @returns {Promise<unknown>|Promise<void>}
     */
    down(element) {
        return this._slide(element, 'down');
    }

    /**
     * Скрыть элемент (slide up).
     *
     * @param element
     * @returns {Promise<unknown>|Promise<void>}
     */
    up(element) {
        return this._slide(element, 'up');
    }

    /**
     * Переключить видимость элемента (либо скрыть, либо показать).
     *
     * @param element
     * @returns {Promise<unknown>|Promise<void>}
     */
    toggle(element) {

        const self = this;

        if (self.isHidden(element)) {
            return self._slide(element, 'down');
        } else {
            return self._slide(element, 'up');
        }

    }

    /**
     * Показать элемент без анимации.
     *
     * @param element
     */
    show(element) {
        element.classList.remove(this._HIDDEN_CLASS_NAME);
    }

    /**
     * Скрыть элемент без анимации.
     *
     * @param element
     */
    hide(element) {
        element.classList.add(this._HIDDEN_CLASS_NAME);
    }

    /**
     * Задаёт скорость выполнения анимации (мс).
     *
     * @param {number} value
     * @returns {module.Slider}
     */
    speed(value) {

        const self = this;

        if (typeof value === 'number') {
            self._speed = value;
        }

        return self;

    }

    /**
     * Задаёт функцию, описывающую плавность выполнения анимации. Например:
     *  - ease        (default) Анимация начинается медленно, затем ускоряется и к концу движения опять замедляется.
     *  - ease-in     Анимация медленно начинается, к концу ускоряется.
     *  - ease-out    Анимация начинается быстро, к концу замедляется.
     *  - ease-in-out Анимация начинается и заканчивается медленно.
     *  - linear      Одинаковая скорость от начала и до конца.
     *
     * @see http://htmlbook.ru/css/transition-timing-function
     * @param {string} value
     * @returns {module.Slider}
     */
    easing(value) {

        const self = this;

        if (typeof value === 'string') {
            self._easing = value;
        }

        return self;

    }

    /**
     * Задаёт задержку перед выполнением анимации.
     *
     * @param {number} value
     * @returns {module.Slider}
     */
    delay(value) {

        const self = this;

        if (typeof value === 'number') {
            self._delay = value;
        }

        return self;

    }

    /**
     * Проверяет, является ли указанный элемент скрытым.
     *
     * @param element
     * @returns {boolean}
     */
    isHidden(element) {
        return element.classList.contains(this._HIDDEN_CLASS_NAME);
    }

    /**
     * @param element
     * @param direction
     * @returns {Promise<unknown>|Promise<void>}
     * @private
     */
    _slide(element, direction) {

        const self = this;

        // Предотвращение запуска "slide down", если он уже выполняется.
        if (direction === 'down' && (
            [...element.classList].some(
                e => new RegExp(/setHeight/).test(e),
            )
            || !self.isHidden(element)
        )) {
            return Promise.resolve();
        }

        // Предотвращение запуска "slide up", если он уже выполняется.
        if (direction === 'up' && (
            self.isHidden(element)
            || [...element.classList].some(
                e => new RegExp(/setHeight/).test(e),
            )
        )) {
            return Promise.resolve();
        }

        const speed = self._speed ? self._speed : 300;

        const style = element.style;
        style.transition = `all ${speed}ms ${self._easing}`;
        style.overflow = 'hidden';

        let contentHeight = element.scrollHeight;

        // Вычитаем отступы из "contentHeight".
        if (direction === 'up') {

            const style = window.getComputedStyle(element);
            const paddingTop = +style.getPropertyValue('padding-top').
                split('px')[0];
            const paddingBottom = +style.getPropertyValue('padding-bottom').
                split('px')[0];

            contentHeight = element.scrollHeight - paddingTop - paddingBottom;

        }

        // Создаём CSS setHeight и внедряем его в HTML.
        const sheet = document.createElement('style');

        // Создаём идентификатор для каждого класса, чтобы разрешить одновременную
        // обработку нескольких элементов, например, при активации циклом forEach.
        const setHeightId = (Date.now() * Math.random()).toFixed(0);
        sheet.innerHTML = `.setHeight-${setHeightId} {height: ${contentHeight}px;}`;
        document.head.appendChild(sheet);

        // Внедряем в элемент CSS-классы для обеспечения отправной точки для анимации.
        if (direction === 'up') {
            element.classList.add(`setHeight-${setHeightId}`);
        } else {
            element.classList.add(self._HIDDEN_CLASS_NAME,
                `setHeight-${setHeightId}`);
        }

        return new Promise(function(resolve) {

            // Добавляем/удаляем CSS-классы для анимации элемента.
            if (direction === 'up') {

                // Ожидание 10 миллисекунд перед добавлением класса
                // предотвращает "прыжок" в высоту при "slide up".
                setTimeout(function() {
                    element.classList.add(self._HIDDEN_CLASS_NAME);
                    resolve();
                }, self._delay ? +self._delay + 10 : 10);

            } else {

                // Ожидание 10 миллисекунд перед добавлением класса
                // предотвращает "зависание" при "slide down".
                setTimeout(function() {
                    element.classList.remove(self._HIDDEN_CLASS_NAME);
                    resolve();
                }, self._delay ? +self._delay + 10 : 10);

            }

        }).then(function() {

            return new Promise(function(resolve) {

                // Удаляем временные стили.
                setTimeout(function() {

                    element.removeAttribute('style');

                    sheet.parentNode.removeChild(sheet);

                    element.classList.remove(`setHeight-${setHeightId}`);

                    resolve(element);

                }, speed);

            });

        });

    }

    /**
     * Включает эффект "затухание".
     *
     * @private
     */
    _turnOnFading() {

        const self = this;
        const styleSheets = document.styleSheets;

        // Сперва вытащим нужную таблицу стилей.
        for (let sIndex = 0; sIndex < styleSheets.length; sIndex++) {

            const sheet = styleSheets.item(sIndex);

            // Вытащить нужно именно "app.css".
            if (sheet.href.split('/').pop() === 'app.css') {

                const maxIndex = (sheet.cssRules.length - 1);
                const selector = `.${self._HIDDEN_CLASS_NAME}`;

                // Затем вытащим нужное правило (перебор в обратном порядке для оптимизации).
                for (let rIndex = maxIndex; rIndex >= 0; rIndex--) {

                    const rule = sheet.cssRules[rIndex];

                    // Для нужного правила добавим свойство "opacity".
                    if (rule.selectorText === selector) {

                        const ruleText = rule.cssText;
                        const propsText = ruleText.substring(
                            ruleText.indexOf('{') + 1,
                            ruleText.indexOf('}'),
                        );

                        let propsArr = propsText.split(';');
                        propsArr = propsArr.map(prop => prop.trim());
                        propsArr = propsArr.filter(prop => prop.length);
                        propsArr.push('opacity: 0 !important;');

                        sheet.removeRule(rIndex);
                        sheet.insertRule(`${selector} {${propsArr.join(';')}}`);

                        break;

                    }

                }

                break;

            }

        }

    }

};
