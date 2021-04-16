<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{

    /**
     * @var string
     */
    private $table = "categories";
    private $comment = 'Хранилище категорий';

    /**
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {

            $table->id();

            $table->string('name', 64)
                ->unique();

            $table->text('description')
                ->nullable();

            $table->json('colors')
                ->nullable()
                ->comment('Основные цвета');

            $table->foreignId('cover_image_id')
                ->default(0)
                ->comment('Обложка');

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
        Schema::dropIfExists('categories');
    }
}
