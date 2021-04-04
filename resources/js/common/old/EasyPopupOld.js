(function () {

	/**
	 * Менеджер простых всплывающих окон. Основные особенности:
	 * - доступные методы: show(), replace() и hide();
	 * - поддерживается расположение окон каскадом (стек);
	 * - поддерживается передача пользовательских данных в окно и между окнами;
	 * - окна автоматически располагаются по центру рабочей области браузера;
	 * - высота окон автоматически подстраивается под размер контента (если контент не влезает, то появляется полоса прокрутки);
	 * - окна не прокручиваются вместе со страницей.
	 *
	 * При вызове метода в него передаётся объект с параметрами, набор которых у каждого метода свой.
	 * Некоторые параметры являются обязательными. В качестве ID передаётся ID элемента в DOM-дереве, в котором
	 * находится контент для всплывающего окна. Механизм такой же, как у функции "popup_show()".
	 *
	 * Пользовательские данные можно передать как через параметр "storage", так и в шаблоне путем указания data-атрибутов
	 * для div-элемента с классом "popup-container". Доступ к этим данным возможен из любой
	 * функции: "before_show(), before_replace(), before_hide()" и "after_show(), after_replace(), after_hide()".
	 *
	 * Пример использования в коде:
	 * (new EasyPopup()).show({
	 * 		id:         'my-awesome-popup',
	 * 		title:      'Мое удивительное всплывающее окно',
	 * 		sub_title:  'Что-то еще важное, в дополнение к заголовку',
	 * 		width:      900,
	 * 		storage:    {
	 * 			some_index:  100500,
	 * 			some_string: 'Тра-та-та'
	 * 		},
	 * 		before_show: function(storage) {
	 * 			debug(storage.some_string);
	 * 			// Какой-то код.
	 * 			storage.some_index += 100;
	 * 		},
	 * 		after_show:  function(storage, overlay) {
	 * 			debug(storage);
	 * 			// Какой-то код.
	 * 			overlay.find('button#my-btn').off('click').on('click', function () {
	 * 				// Какой-то код.
	 * 				storage.some_index += 100;
	 * 			});
	 * 		}
	 * });
	 *
	 * Остальные методы вызываются по аналогии.
	 *
	 * @constructor
	 */
	function EasyPopup() {

		this.popup_params = {}; // Параметры всплывающего окна.
		this.storage = {}; // Хранилище пользовательских данных.

		this.stack = $('<div id="easy-popup-stack"></div>');

		// Составные части всплывающего окна.
		this.overlay = $('<div class="easy-popup-overlay"></div>');
		this.overlay_wrapper = $('<div class="wrapper"></div>');
		this.overlay_container = $('<div class="container"></div>');
		this.overlay_header = $('<div class="header"><div class="close"><i class="fa fa-times"></i></div><h3></h3><div class="subtitle"></div></div>');
		this.overlay_content = $('<div class="content"></div>');

		// Начальный z-index для самого первого оверлея.
		this.overlay_index = 8000;
		// Величина, применяемая в расчете максимальной высоты всплывающего окна.
		this.window_height_delta = 200;

		this.init();

	}

	/**
	 * Инициализация всплывающих окон.
	 *
	 * @private
	 */
	EasyPopup.prototype.init = function () {

		var self = this;

		var body = $('body');
		var existing_stack = body.find('div#easy-popup-stack');

		if (existing_stack.length) { // Singleton
			self.stack = existing_stack;
		} else {
			body.append(self.stack);
		}

		// Инициализация хранилища пользовательских данных.
		if (typeof self.stack.data('storage') !== 'undefined') {
			self.storage = self.stack.data('storage');
		}

		// Корректировка максимальной высоты области для контента во всех открытых всплывающих окнах.
		$(window).resize(function () {

			var window_height = $(window).height() - self.window_height_delta;

			self.stack.find('div.easy-popup-overlay').each(function () {
				$(this).find('div.content').css({
					maxHeight: window_height
				});
			});

		});

	};

	/**
	 * Добавляет всплывающее окно в стек и отображает его.
	 * Обязательный параметр: "id".
	 *
	 * @param {Object} params
	 */
	EasyPopup.prototype.show = function (params) {

		var self = this;

		var default_params = {
			id:          '', // (Обязательный) ID открываемого окна, который должен совпадать с ID элемента, в котором расположен контент для всплывающего окна.
			title:       '', // Заголовок всплывающего окна.
			sub_title:   '', // Дополнение к заголовку окна.
			width:       0, // Ширина всплывающего окна. Если не указать, то окно примет ширину автоматически, опираясь на ширину пользовательского контента.
			storage:     {}, // Объект с пользовательскими данными, которые нужно передать в стек.
			content:     '', // HTML-контент, который будет смонтирован в открытое окно. Поддерживается макрос: "loading".
			before_show: null, // Функция, которая будет вызвана до монтирования окна на страницу.
			after_show:  null, // Функция, которая будет вызвана после монтирования окна на страницу.
			fade_in:     true // Мягкое появление окна. Если указать "false", то окно появится сразу.
		};

		if (typeof params === 'object' && Object.keys(params).length) {
			self.popup_params = $.extend(default_params, params);
		}

		if (self.popup_params.id) {

			// Вызываем функцию до появления окна.
			if (typeof self.popup_params.before_show === 'function') {
				self.popup_params.before_show(self.storage);
			}

			// Монтируем окно на страницу.
			self.append_to_stack();

		}

		return false;

	};

	/**
	 * Выполняет замену контента в открытом всплывающем окне.
	 * Обязательные параметры: "id" и "content".
	 *
	 * @param {Object} params
	 * @void
	 */
	EasyPopup.prototype.replace = function (params) {

		var self = this;

		var default_params = {
			id:             '', // ID открытого всплывающего окна.
			title:          '', // Новый заголовок всплывающего окна. Чтобы скрыть заголовок, нужно передать "hide".
			sub_title:      '', // Новое дополнение к заголовку окна. Чтобы скрыть подзаголовок, нужно передать "hide".
			width:          0, // Новая ширина всплывающего окна. Если не указать, то значение ширины останется прежним.
			storage:        {}, // Объект с пользовательскими данными, которые нужно передать в стек.
			content:        '', // HTML-контент, который заместит собой существующий контент в указанном окне. Поддерживается макрос: "loading".
			before_replace: null, // Функция, которая будет вызвана до замены контента в окне.
			after_replace:  null // Функция, которая будет вызвана после замены контента в окне.
		};

		if (typeof params === 'object' && Object.keys(params).length) {
			self.popup_params = $.extend(default_params, params);
		}

		if (self.popup_params.id && self.popup_params.content) {

			// Вызываем функцию до появления окна.
			if (typeof self.popup_params.before_replace === 'function') {
				self.popup_params.before_replace(self.storage);
			}

			// Выполняем замену контента.
			self.replace_content();

		}

		return false;

	};

	/**
	 * Скрывает всплывшее окно и удаляет его из стека.
	 * Обязательных параметров нет.
	 *
	 * @param {Object} [params]
	 */
	EasyPopup.prototype.hide = function (params) {

		var self = this;

		var default_params = {
			id:          '', // ID открытого всплывающего окна. Если не указать, то будут закрыты все окна, имеющиеся в стеке.
			before_hide: null, // Функция, которая будет вызвана до скрытия окна со страницы.
			after_hide:  null, // Функция, которая будет вызвана после скрытия окна со страницы.
			fade_out:    false // Мягкое закрытие окна (по умолчанию отключено). Если указать "true", то окно будет "растворяться".
		};

		if (typeof params === 'object' && Object.keys(params).length) {
			self.popup_params = $.extend(default_params, params);
		}

		var fade_out_duration = self.popup_params.fade_out ? 300 : 0;

		// Вызываем функцию до скрытия окна.
		if (typeof self.popup_params.before_hide === 'function') {
			self.popup_params.before_hide(self.storage);
		}

		// Если ID окна не указан, то закроем все окна.
		if (self.popup_params.id === '') {

			self.stack.find('div.easy-popup-overlay').each(function () {
				$(this).fadeOut(fade_out_duration, function () {

					// Вызываем функцию после скрытия окна.
					if (typeof self.popup_params.after_hide === 'function') {
						self.popup_params.after_hide(self.storage, $(this));
					}

					$(this).remove();

				});
			});

		} else { // Закрываем только определенное окно.

			self.stack.find('div#' + self.popup_params.id + '-overlay').fadeOut(fade_out_duration, function () {

				// Вызываем функцию после скрытия окна.
				if (typeof self.popup_params.after_hide === 'function') {
					self.popup_params.after_hide(self.storage, $(this));
				}

				$(this).remove();

			});

		}

		return false;

	};

	/**
	 * Итератор закрытия всплывающих окон.
	 */
	EasyPopup.prototype.hide_from_stack = function () {

		var self = this;

		if (self.get_popup_opened_count()) {
			self.hide({
				id: self.stack.find('div.easy-popup-overlay:first').attr('id').replace(new RegExp("-overlay", 'g'), "")
			});
		}

	};

	/**
	 * Возвращает количество открытых всплывающих окон. Своего рода проверка: есть ли открытые окна?
	 *
	 * @return {number}
	 */
	EasyPopup.prototype.get_popup_opened_count = function () {

		return this.stack.find('div.easy-popup-overlay').length;

	};

	/**
	 * Собирает всплывающее окно, монтирует его в стек и отображает.
	 *
	 * @private
	 */
	EasyPopup.prototype.append_to_stack = function () {

		var self = this;

		var popup_content = $('div#' + self.popup_params.id + ':first').clone(true);

		if (popup_content.length) {

			var stack_index = self.stack.find('div.easy-popup-overlay').length; // Номер для z-index.
			var overlay = self.overlay.clone();
			var overlay_wrapper = self.overlay_wrapper.clone();
			var overlay_container = self.overlay_container.clone();
			var overlay_header = self.overlay_header.clone();
			var overlay_content = self.overlay_content.clone();
			var fade_in_duration = self.popup_params.fade_in ? 200 : 0;

			self.overlay_close_init(overlay); // Обработчик клика по оверлею.

			if (parseInt(self.popup_params.width, 10) > 0) {
				overlay_container.width(self.popup_params.width);
			}

			// Нужно ли показать заголовок окна.
			if (self.popup_params.title) {

				overlay_header.find('h3').text(self.popup_params.title);

				self.close_init(overlay_header.find('div.close')); // Обработчик клика по "крестику".

				if (self.popup_params.sub_title) {
					overlay_header.find('div.subtitle').text(self.popup_params.sub_title);
				} else {
					overlay_header.find('div.subtitle').hide();
				}

				overlay_container.append(overlay_header);

			}

			// Если в параметрах имеется контент для отображения в окне, то существующий контент будет замещен.
			if (self.popup_params.content) {
				self.process_macro();
				popup_content.empty().append(self.popup_params.content);
			}

			overlay_content.append(popup_content.show()); // Добавляем контент в окно.
			overlay_content.css({
				maxHeight: $(window).height() - self.window_height_delta
			});

			overlay_container.append(overlay_content);

			overlay.css({
				zIndex:  self.overlay_index + stack_index,
				opacity: 0
			});
			overlay.attr('id', self.popup_params.id + '-overlay');

			// Добавим в стек пользовательские данные (если таковые имеются).
			var user_data_from_content = Object.keys(popup_content.data()); // Данные из шаблона (см. описание класса).

			if (user_data_from_content.length) {
				$.each(user_data_from_content, function (index, item) {
					self.popup_params.storage[item] = popup_content.data(item);
				});
			}

			if (Object.keys(self.popup_params.storage)) {
				self.storage = $.extend(self.storage, self.popup_params.storage);
			}

			if (Object.keys(self.storage)) {
				self.stack.data('storage', self.storage);
			}
			// /Пользовательские данные.

			overlay_wrapper.append(overlay_container);
			overlay.append(overlay_wrapper);

			self.stack.prepend(overlay); // Монтируем в стек и показываем окно.

			self.stack.find('div#' + self.popup_params.id + '-overlay').animate({ opacity: 1 }, fade_in_duration, function () {

				// Вызываем функцию после появления окна.
				if (typeof self.popup_params.after_show === 'function') {
					self.popup_params.after_show(self.storage, $(this));
				}

				self.overlay_adapt_css(overlay);

			});

		} else {
			debug('Элемент не найден: #' + self.popup_params.id);
		}

	};

	/**
	 * Обновляет контент в открытом окне.
	 *
	 * @private
	 */
	EasyPopup.prototype.replace_content = function () {

		var self = this;

		var overlay = self.stack.find('div#' + self.popup_params.id + '-overlay');

		if (overlay.length) {

			// Нужно ли изменить ширину окна.
			if (self.popup_params.width) {

				if (self.popup_params.width === -1) {
					overlay.find('div.container').css('width', '');
				} else {
					overlay.find('div.container').width(self.popup_params.width);
				}

			}

			var overlay_header = overlay.find('div.header');

			if (overlay_header.length) { // Нужно ли изменить заголовок и/или подзаголовок окна.

				if (self.popup_params.title) {

					if (self.popup_params.title === 'hide') {
						overlay_header.hide();
					} else {
						overlay_header.find('h3').text(self.popup_params.title);
					}

				}

				if (self.popup_params.sub_title) {

					if (self.popup_params.sub_title === 'hide') {
						overlay_header.find('div.subtitle').hide();
					} else {
						overlay_header.find('div.subtitle').text(self.popup_params.sub_title).show();
					}

				}

			} else { // Добавляем шапку для окна, если требуется.

				overlay_header = self.overlay_header.clone();

				if (self.popup_params.title) {

					overlay_header.find('h3').text(self.popup_params.title);

					self.close_init(overlay_header.find('div.close')); // Обработчик клика по "крестику".

					if (self.popup_params.sub_title) {
						overlay_header.find('div.subtitle').text(self.popup_params.sub_title);
					} else {
						overlay_header.find('div.subtitle').hide();
					}

					overlay.find('div.container').prepend(overlay_header);

				}
			}

			// Пользовательские данные в стеке.
			if (Object.keys(self.popup_params.storage)) {
				self.storage = $.extend(self.storage, self.popup_params.storage);
			}

			if (Object.keys(self.storage)) {
				self.stack.data('storage', self.storage);
			}

			// Выполняем замену контента.
			self.process_macro();
			var overlay_content = overlay.find('div.content');
			overlay_content.empty().append(self.popup_params.content);

			// Вызываем функцию после обновления контента.
			if (typeof self.popup_params.after_replace === 'function') {
				self.popup_params.after_replace(self.storage, overlay);
			}

			self.overlay_adapt_css(overlay);

		}

	};

	/**
	 * Инициализирует обработчик клика по оверлею для закрытия окна.
	 *
	 * @param {object} overlay_obj
	 * @private
	 */
	EasyPopup.prototype.overlay_close_init = function (overlay_obj) {

		var self = this;

		if (overlay_obj.length) {

			overlay_obj.off('click').on('click', function (e) {

				if ($(e.target).hasClass('easy-popup-overlay')) {
					self.hide({
						id: self.popup_params.id
					});
				}

			});

		}

	};

	/**
	 * Инициализирует обработчик кнопки "закрыть" (крестик) в заголовке всплывающего окна.
	 *
	 * @param {object} button_obj
	 * @private
	 */
	EasyPopup.prototype.close_init = function (button_obj) {

		var self = this;

		if (button_obj.length) {

			button_obj.off('click').on('click', function () {
				self.hide({
					id: self.popup_params.id
				});
			});

		}

	};

	/**
	 * Обработка контента: выполняет замену макросов на соответствующий HTML-код.
	 *
	 * @private
	 */
	EasyPopup.prototype.process_macro = function () {

		var self = this;

		if (self.popup_params.content === 'loading') {
			self.popup_params.content = '<div class="popup-loading"></div>';
		}

	};

	/**
	 * Адаптирует CSS-стили для корректного отображения контента во всплывающем окне.
	 *
	 * @param $overlay
	 * @private
	 */
	EasyPopup.prototype.overlay_adapt_css = function ($overlay) {

		// Корректировка поля для ввода тегов: выпадающий список.
		var $tags_input = $overlay.find('.tags-input');

		if ($tags_input.length) {
			$tags_input.width($tags_input.width());
			$tags_input.find('.tags-list').css({
				position: 'fixed',
				width:    'inherit'
			});
		}
		// /Корректировка поля для ввода тегов.

	};

	window.EasyPopup = EasyPopup;

}());
