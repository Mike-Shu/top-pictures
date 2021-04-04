class EasyPopup {

    constructor() {

        this.popup_params = {}; // Параметры всплывающего окна.
        this.storage = {}; // Хранилище пользовательских данных.

        this.stack = $('<div id="easy-popup-stack"></div>');
        this.stack = document.createElement('<div id="easy-popup-stack"></div>');

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

        // this.init();

    }

}
