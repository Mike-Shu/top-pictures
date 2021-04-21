<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{

    /**
     * @var string
     */
    private $table = "images";
    private $comment = 'Хранилище фотографий';

    /**
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                ->default(0)
                ->index()
                ->comment('В какой категории');

            $table->foreignId('user_id')
                ->index()
                ->comment('Владелец файла');

            $table->string('name', 32)
                ->unique()
                ->comment('Исходный файл');

            $table->string('extension', 4)
                ->index()
                ->comment('Расширение файла');

            $table->unsignedInteger('size')
                ->comment('Размер файла');

            $table->boolean('processed')
                ->default(false)
                ->index()
                ->comment('Обработано?');

            $table->text('description')
                ->nullable();

            $table->unsignedSmallInteger('width')
                ->index()
                ->comment('Ширина изображения');

            $table->unsignedSmallInteger('height')
                ->index()
                ->comment('Высота изображения');

            $table->json('palette')
                ->nullable()
                ->comment('Цветовая палитра');

            $table->timestamps();
            $table->softDeletes();

        });

        set_table_comment($this->table, $this->comment);
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
