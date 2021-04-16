const debug = require('../common/debug');
const Resumable = require('./Resumable');
const UploaderException = require('./UploaderException');
const ResponseException = require('./ResponseException');
const Slider = require('../common/Slider');
const formatBytes = require('../common/formatBytes');

/**
 * Приложение для загрузки файлов.
 */
module.exports = class Uploader {

    constructor(logger) {

        const self = this;

        /**
         * Для нужд логирования.
         */
        self.logger = logger;

        /**
         * Анимация "slide up" & "slide down".
         * @type {module.Slider}
         */
        self.slider = new Slider(true);

        /**
         * Набор основных параметров для приложения. Настраивается в "config/interface.php".
         * @type {*}
         */
        self.config = self._getConfig('input[name=_config]');

        /**
         * Проинициализируем плагин-загрузчик.
         *
         * @see https://github.com/23/resumable.js
         * @type {Resumable}
         */
        self.Loader = new Resumable({
            fileType:                    self.config.fileType,
            chunkSize:                   self.config.chunkSize,
            testChunks:                  self.config.testChunks,
            maxChunkRetries:             self.config.maxChunkRetries,
            simultaneousUploads:         self.config.simultaneousUploads,
            maxFileSize:                 self.config.maxFileSize,
            prioritizeFirstAndLastChunk: true,
            target:                      self._getUploadUrl(
                'input[name=_upload_url]'),
            query:                       () => {
                return {
                    _token:      self._getToken('input[name=_token]'),
                    category_id: self._getSelectedId('select[name=category_id'),
                };
            },
            generateUniqueIdentifier:    self._generateUniqueIdentifier,
            fileTypeErrorCallback:       self._fileTypeErrorCallback,
            minFileSizeErrorCallback:    self._minFileSizeErrorCallback,
            maxFileSizeErrorCallback:    self._maxFileSizeErrorCallback,
            maxFiles:                    self.config.maxFiles,
            maxFilesErrorCallback:       self._maxFilesErrorCallback,
        });

        // Убедимся, что браузер пользователя умеет работать с расширенным HTML.
        if (self.Loader.support) {
            self._init();
        } else {
            self._browserNotSupported();
        }

    }

    /**
     * Возвращает набор основных параметров для приложения.
     *
     * @param {string} selector
     * @returns {any}
     * @private
     */
    _getConfig(selector) {

        const $config = this._getObjectBySelector(selector,
            'an error occurred while retrieving the config value');

        return JSON.parse($config.value.trim());

    }

    /**
     * Инициализация приложения.
     *
     * @private
     */
    _init() {

        const self = this;

        // Назначим загрузчику кнопку "Обзор".
        self.Loader.assignBrowse(
            self._getObjectBySelector(
                '#browse-button',
                'an error occurred while retrieving the "Browse button" object',
            ),
        );

        // Назначим загрузчику область "Для перетаскивания".
        self.Loader.assignDrop(
            self._getObjectBySelector(
                '#drop-area',
                'an error occurred while retrieving the "Drop area" object',
            ),
        );

        // Контейнер со списком файлов.
        self.$uploadingContainer = self._getObjectBySelector(
            '#uploading-container',
            'an error occurred while retrieving the "Uploading container" object',
        );

        // Список файлов для загрузки.
        self.$uploadingList = self._getObjectBySelector(
            '#uploading-list',
            'an error occurred while retrieving the "Uploading list" object',
        );

        // Счетчик файлов в списке для загрузки.
        self.$uploadingListCount = self._getObjectBySelector(
            '#uploading-list-count',
            'an error occurred while retrieving the "Uploading list count" object',
        );

        // Кнопка "Закачать файлы".
        self.$startButton = self._getObjectBySelector(
            '#uploader-start-list',
            'an error occurred while retrieving the "Start button" object',
        );

        // Кнопка "Очистить список".
        self.$clearButton = self._getObjectBySelector(
            '#uploader-clear-list',
            'an error occurred while retrieving the "Clear button" object',
        );

        // Кнопка "Прекратить загрузку".
        self.$stopButton = self._getObjectBySelector(
            '#uploader-stop-list',
            'an error occurred while retrieving the "Stop button" object',
        );

        // HTML-шаблон файла для добавления в список загрузки.
        self.listItemTemplate = null;

        self._controlInit();

    }

    /**
     * Инициализация управления приложением.
     *
     * @private
     */
    _controlInit() {

        const self = this;

        // Получить HTML-шаблон файла для добавления в список загрузки.
        self._getListItemTemplate();

        // Обработчик для кнопки "Закачать файлы".
        self.$startButton.addEventListener('click', e => {

            try {
                self._startUploadingFiles(e);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Обработчик для кнопки "Очистить список".
        self.$clearButton.addEventListener('click', e => {

            try {
                self._clearList(e);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Обработчик для кнопки "Остановить загрузку".
        self.$stopButton.addEventListener('click', e => {

            try {
                self._stopUploadingFiles(e);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Обработчик добавленных файлов (файлы были добавлены через диалог, либо добавлены перетаскиванием).
        self.Loader.on('filesAdded', (arrayAdded, arraySkipped) => {

            try {
                self._filesAdded(arrayAdded, arraySkipped);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Загрузка файлов начата.
        self.Loader.on('uploadStart', () => {

            try {
                self._uploadStart();
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Загрузка файлов завершена.
        self.Loader.on('complete', () => {

            try {
                self._uploadComplete();
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Процесс загрузки одного файла.
        self.Loader.on('fileProgress', file => {

            try {
                self._fileProgress(file);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // Один файл был успешно загружен.
        self.Loader.on('fileSuccess', (file, message) => {

            try {
                self._fileSuccess(file);
                debug(file);
                debug(message);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        // В процессе загрузки файлов что-то пошло не так.
        self.Loader.on('error', (message, file) => {

            try {
                debug('Error: ' + message);
            } catch (err) {
                self.logger.exception(err);
            }

        });

        self._uploaderListControlEnabled(
            true,
            true,
            false,
            true,
        );

    }

    /**
     * Вывод информации о том, что браузер пользователя не поддерживается.
     *
     * @private
     */
    _browserNotSupported() {

        const self = this;

        const $uploader = self._getObjectBySelector('#uploader',
            'an error occurred while retrieving the "uploader" object');

        const url = self._getNotSupportUrl('input[name=_not_supported_url]');

        self._fetchHtml(url, html => {
            $uploader.replaceWith(
                html.querySelector('#uploader-not-supported'));
        });

    }

    /**
     * Добавляет файлы в список загрузки.
     *
     * @param {Array} arrayAdded
     * @param {Array} arraySkipped
     * @private
     */
    _filesAdded(arrayAdded, arraySkipped) {

        const self = this;

        if (!self.listItemTemplate) {
            throw new UploaderException(
                'The "listItem" template is not specified');
        }

        if (arrayAdded.length) {

            // Покажем список для загрузки.
            self.slider.show(self.$uploadingContainer);

            // Добавим выбранные файлы в список загрузки.
            arrayAdded.forEach(file => {

                const template = self.listItemTemplate.cloneNode(true);
                const $uploadItem = template.querySelector('.uploader-item');

                $uploadItem.setAttribute(
                    'id',
                    file.uniqueIdentifier,
                );

                template.querySelector('.uploader-item-name').
                    innerHTML = `${file.fileName} / ${formatBytes(file.size)}`;

                self.$uploadingList.append(
                    template.querySelector('.uploader-item'),
                );

            });

            // Подсчитаем кол-во файлов.
            self._updateListCount();

            // Назначим обработчики для кнопки "Удалить".
            self.$uploadingList.
                querySelectorAll('.uploader-item-remove').
                forEach(_item => {

                    _item.addEventListener('click', e => {

                        e.preventDefault();

                        const button = e.target;
                        const $item = button.closest('.uploader-item');

                        button.disabled = true;
                        self._removeItemFromList($item);

                        // Скроем список для загрузки.
                        if (!self.Loader.files.length) {
                            self.slider.up(self.$uploadingContainer);
                        }

                    });

                });

        }

        // Список проигнорированных файлов (пусть просто полежит здесь).
        arraySkipped.forEach(file => {
            this.logger.console(file.fileName);
        });

    }

    /**
     * Получает HTML-шаблон файла, добавленного в список загрузки.
     *
     * @private
     */
    _getListItemTemplate() {

        const self = this;
        const url = self._getItemUrl('input[name=_item_url]');

        self._fetchHtml(url, html => {
            self.listItemTemplate = html;
        });

    }

    /**
     * Получает HTML-файл и передаёт его в замыкание.
     *
     * @param {string} url
     * @param {function} callback
     * @private
     */
    _fetchHtml(url, callback) {

        const self = this;

        fetch(url).
            then(response => {

                if (!response.ok) {
                    throw new ResponseException(response);
                }

                return response.text();

            }).
            then(html => {

                const Parser = new DOMParser();
                const Document = Parser.parseFromString(html, 'text/html');

                callback(Document);

            }).
            catch(err => {
                self.logger.exception(err);
            });

    }

    /**
     * Запуск процесса загрузки файлов.
     *
     * @param {Event} e
     * @private
     */
    _startUploadingFiles(e) {

        e.preventDefault();

        const self = this;

        if (self.Loader.files.length) {
            self.Loader.upload();
        }

    }

    /**
     * Останавливает процесс загрузки файлов.
     *
     * @param {Event} e
     * @private
     */
    _stopUploadingFiles(e) {

        e.preventDefault();

        const self = this;

        self.Loader.pause();

        self._uploaderListControlEnabled(
            true,
            true,
            false,
            true,
        );

    }

    /**
     * Возвращает объект с одним файлом из списка загрузки.
     *
     * @param {Object} file
     * @returns {*}
     * @private
     */
    _getListItemById(file) {

        const self = this;
        const selector = `#${file.uniqueIdentifier}`;

        return self._getObjectBySelector(selector,
            'an error occurred while retrieving the "list item" object');

    }

    /**
     * Процесс загрузки файла.
     *
     * @param {Object} file
     * @private
     */
    _fileProgress(file) {

        const self = this;
        const $fileItem = self._getListItemById(file);
        const $progressBarValue = $fileItem.querySelector('.progress-value');
        const progressValue = Math.floor(file.progress() * 100);

        $progressBarValue.style.width = `${progressValue}%`;

    }

    /**
     * Файл успешно загружен.
     *
     * @param {Object} file
     * @private
     */
    _fileSuccess(file) {

        const self = this;
        const $fileItem = self._getListItemById(file);

        self.slider.
            up($fileItem).
            then(() => {
                self._removeItemFromList($fileItem, () => {
                    self._updateListCount();
                });
            });

    }

    /**
     * Загрузка файлов начата.
     *
     * @private
     */
    _uploadStart() {

        const self = this;

        self._uploaderListControlEnabled(
            false,
            false,
            true,
        );

    }

    /**
     * Загрузка файлов завершена.
     *
     * @private
     */
    _uploadComplete() {

        const self = this;

        self._uploaderListControlEnabled(
            true,
            true,
            false,
        );

        setTimeout(() => {

            if (!self.Loader.files.length) {

                self.slider.
                    easing(self.slider.EASE_IN).
                    up(self.$uploadingContainer).
                    then(() => {
                        // Some...
                    });

            }

        }, 1000);

    }

    /**
     * Удаляет из списка загрузки все файлы.
     *
     * @param {Event} e
     * @private
     */
    _clearList(e) {

        e.preventDefault();

        const self = this;

        // Скроем список для загрузки, и...
        self.slider.
            easing(self.slider.EASE_IN_OUT).
            up(self.$uploadingContainer).
            then(() => {

                // ... удалим все файлы из списка.
                self.$uploadingList.
                    querySelectorAll('.uploader-item').
                    forEach($_item => {
                        self._removeItemFromList($_item);
                    });

            });

    }

    /**
     * Удаляет из списка загрузки один файл.
     *
     * @param {HTMLObjectElement} $item
     * @param {function} [callback]
     * @private
     */
    _removeItemFromList($item, callback) {

        const self = this;

        const fileId = $item.getAttribute('id');
        const file = self.Loader.getFromUniqueIdentifier(fileId);

        if (file) {

            self.Loader.removeFile(file);

            self.slider.
                easing(self.slider.EASE_OUT).
                up($item).
                then(() => {

                    $item.remove();

                    if ('function' === typeof callback) {
                        callback();
                    }

                });

        }

    }

    /**
     * Обновляет количество файлов, имеющихся в списке для загрузки,
     * и возвращает новое значение.
     *
     * @private
     */
    _updateListCount() {

        const self = this;

        self.$uploadingListCount.innerHTML = self.Loader.files.length;

    }

    /**
     * Переключает доступность кнопок управления.
     *
     * @param {boolean} startBtn Кнопка "Закачать файлы".
     * @param {boolean} clearBtn Кнопка "Очистить список".
     * @param {boolean} stopBtn Кнопка "Прекратить загрузку".
     * @param {boolean} removeBtn Кнопка "Удалить файл".
     * @private
     */
    _uploaderListControlEnabled(
        startBtn, clearBtn, stopBtn, removeBtn = false) {

        const self = this;

        self.$startButton.disabled = !startBtn;
        self.$clearButton.disabled = !clearBtn;
        self.$stopButton.disabled = !stopBtn;

        self.$uploadingList.
            querySelectorAll('.uploader-item-remove').
            forEach(($_item) => {
                $_item.disabled = !removeBtn;
            });

    }

    /**
     * Возвращает уникальный идентификатор для загружаемого файла.
     *
     * @param {Object} file
     * @param {Object} event
     * @returns {string}
     * @private
     */
    _generateUniqueIdentifier(file, event) {

        const relativePath = // Некоторая путаница в разных версиях Firefox.
            file.webkitRelativePath ||
            file.relativePath ||
            file.fileName ||
            file.name;

        const path = relativePath.replace(/[^0-9a-zA-Z]/img, '').toLowerCase();

        return `file-${file.size}-${path}`;

    }

    /**
     * Ошибка: выбран файл, запрещенный к загрузке.
     *
     * @param {Object} file
     * @param {number} errorCount
     * @private
     */
    _fileTypeErrorCallback(file, errorCount) {
        debug('Error file type: ' + file.name);
    }

    /**
     * Ошибка: выбранный файл слишком малого размера.
     *
     * @param {Object} file
     * @param {number} errorCount
     * @private
     */
    _minFileSizeErrorCallback(file, errorCount) {

        // const self = this;

        debug(`Error filesize: ${file.size}`);

    }

    /**
     * Ошибка: выбранный файл слишком большого размера.
     *
     * @param {Object} file
     * @param {number} errorCount
     * @private
     */
    _maxFileSizeErrorCallback(file, errorCount) {

        const self = this;

        debug(`Error filesize: ${file.size} out of ${self.config.maxFileSize}`);

    }

    /**
     * Ошибка: превышен лимит на максимальное количество файлов, загружаемых за один раз.
     *
     * @param {Array} files
     * @param {number} errorCount
     * @private
     */
    _maxFilesErrorCallback(files, errorCount) {

        const self = this;

        debug(
            `Error files count: ${files.length} out of ${self.config.maxFiles}`);

    }

    /**
     * Возвращает CSRF-токен.
     *
     * @param {string} selector
     * @returns {*}
     * @private
     */
    _getToken(selector) {

        const $token = this._getObjectBySelector(selector,
            'an error occurred while retrieving the token value');

        return $token.value.trim();

    }

    /**
     * Возвращает URL для загрузки файлов.
     *
     * @param {string} selector
     * @returns {*}
     * @private
     */
    _getUploadUrl(selector) {

        const $url = this._getObjectBySelector(selector,
            'an error occurred while retrieving the upload URL value');

        return $url.value.trim();

    }

    /**
     * Возвращает URL для получения "uploader-list-item".
     *
     * @param {string} selector
     * @returns {*}
     * @private
     */
    _getItemUrl(selector) {

        const $url = this._getObjectBySelector(selector,
            'an error occurred while retrieving the item URL value');

        return $url.value.trim();

    }

    /**
     * Возвращает URL для получения "browser-not-supported".
     *
     * @param {string} selector
     * @returns {*}
     * @private
     */
    _getNotSupportUrl(selector) {

        const $url = this._getObjectBySelector(selector,
            'an error occurred while retrieving the support URL value');

        return $url.value.trim();

    }

    /**
     * Возвращает ID категории, в которую будут добавлены загруженные изображения.
     *
     * @param selector
     * @returns {*}
     * @private
     */
    _getSelectedId(selector) {

        const $select = this._getObjectBySelector(selector,
            'an error occurred while retrieving the category id value');

        return $select.value.trim();

    }

    /**
     * Возвращает объект на основе переданного селектора.
     *
     * @param {string} selector
     * @param {string} exceptMsg
     * @returns {*}
     * @private
     */
    _getObjectBySelector(selector, exceptMsg) {

        const s = selector.trim();
        const msg = exceptMsg.trim();

        if (!s) {
            throw new UploaderException(
                'the "selector" parameter was not specified');
        }

        if (!msg) {
            throw new UploaderException(
                'the "exceptMsg" parameter was not specified');
        }

        const $obj = document.querySelector(s);

        if (!$obj) {
            throw new UploaderException(msg);
        }

        return $obj;

    }

};
