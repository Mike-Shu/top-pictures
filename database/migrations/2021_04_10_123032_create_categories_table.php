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
    private $comment = '';

    /**
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {

            $table->id();

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('amount')
                ->default(0)
                ->comment('Количество изображений');

            $table->json('colors')
                ->nullable()
                ->comment('Основные цвета');

            $table->timestamps();
            $table->softDeletes();

        });

        $prefix = config('database.connections.mysql.prefix');
        $tableName = $prefix . $this->table;

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` comment 'Хранилище фотографий'");
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
