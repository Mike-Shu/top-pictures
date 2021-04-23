<?php

return [
    /**
     * Категория.
     */
    'category' => [

        // Количество категорий на одной странице.
        'per_page'        => 7,
        // Максимальная длина названия для категории.
        'name_max_length' => 64,
        // Максимальная длина описания для категории.
        'desc_max_length' => 512,

    ],

    'image'     => [

        // Количество изображений на одной странице.
        'per_page'             => 7,
        // Максимальная длина имени файла, который будет отдан пользователю при скачивании изображения.
        'download_name_length' => 128,

    ],
    /**
     * Загрузчик изображений.
     */
    'uploading' => [

        // Хранилище.
        'storage'   => [
            // Диск для хранения загруженных изображений (хранилище). Например: "local" или "s3".
            'disk' => env('UPLOADER_STORAGE_DISK', 'local'),
            // Каталог на диске (относительный путь внутри хранилища). Например: "some/path".
            'path' => env('UPLOADER_STORAGE_PATH', 'images'),
        ],

        // Миниатюры.
        'thumbs'    => [
            // Диск для хранения сгенерированных миниатюр. Например: "public" или "s3".
            'disk'   => env('UPLOADER_THUMBS_STORAGE_DISK', 'public'),
            // Основной каталог на диске (относительный путь внутри диска). Например: "thumbs".
            'path'   => env('UPLOADER_THUMBS_STORAGE_PATH', 'thumbs'),
            'large'  => [
                // Каталог для больших миниатюр (относительный путь внутри основного каталога).
                'path'    => env('UPLOADER_THUMBS_LARGE_PATH', 'large'),
                // Ширина миниатюры (px).
                'width'   => env('UPLOADER_THUMBS_LARGE_WIDTH', 1168),
                // Качество миниатюры при сжатии файла.
                'quality' => env('UPLOADER_THUMBS_LARGE_QUALITY', 80),
            ],
            'middle' => [
                // Каталог для средних миниатюр (относительный путь внутри основного каталога).
                'path'    => env('UPLOADER_THUMBS_MIDDLE_PATH', 'middle'),
                // Ширина миниатюры (px).
                'width'   => env('UPLOADER_THUMBS_MIDDLE_WIDTH', 400),
                // Качество миниатюры при сжатии файла.
                'quality' => env('UPLOADER_THUMBS_MIDDLE_QUALITY', 80),
            ],
            'small'  => [
                // Каталог для средних миниатюр (относительный путь внутри основного каталога).
                'path'    => env('UPLOADER_THUMBS_SMALL_PATH', 'small'),
                // Ширина миниатюры (px).
                'width'   => env('UPLOADER_THUMBS_SMALL_WIDTH', 100),
                // Качество миниатюры при сжатии файла.
                'quality' => env('UPLOADER_THUMBS_SMALL_QUALITY', 80),
            ],
        ],

        /**
         * Набор основных параметров для фронт-энд части загрузчика (resumable.js).
         * Имена параметров оригинальные (смотри доку: https://github.com/23/resumable.js#configuration).
         */
        'resumable' => [
            // MIME-типы файлов, которые допущены для загрузки в хранилище.
            'fileType'                    => [
                'image/jpeg',
            ],
            // Сколько файлов можно загрузить за один сеанс (длина очереди).
            'maxFiles'                    => 1000,
            // Максимальный размер одного файла (Mб).
            'maxFileSize'                 => (1048576 * 10),
            // Размер в байтах каждого загруженного фрагмента данных (Мб).
            'chunkSize'                   => (1048576 * 1),
            // Максимальное количество повторных попыток для фрагмента до того, как загрузка будет неудачной (по умолчанию: не лимитировано).
            'maxChunkRetries'             => 3,
            // Количество одновременных загрузок фрагментов (по умолчанию: 3).
            'simultaneousUploads'         => 5,
            // Приоритет для первого и последнего фрагмента файла.
            'prioritizeFirstAndLastChunk' => true,
            // GET-запрос к серверу для каждого фрагмента, чтобы узнать, не существует ли он уже (по умолчанию: true).
            'testChunks'                  => false,
        ]

    ]
];
