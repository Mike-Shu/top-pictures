<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class TraitMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * @var string
     */
    protected $description = 'Create a new trait class';

    /**
     * @var string
     */
    protected $type = 'Trait';

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return $this->laravel->basePath('/stubs/trait.stub');
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Traits';
    }
}
