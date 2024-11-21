<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeDTO extends Command
{
    protected $signature = 'make:dto {name}'; // Команда будет выглядеть как "php artisan make:dto ClassName"
    
    protected $description = 'Создать новый DTO класс';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        // Определение пути к DTO
        $path = app_path("DTO/{$name}.php");

        // Проверка, существует ли уже такой класс
        if ($this->files->exists($path)) {
            $this->error("DTO с именем {$name} уже существует!");
            return false;
        }

        // Создание папки, если её нет
        $this->makeDirectory(dirname($path));

        // Создание файла DTO с базовым шаблоном
        $stub = $this->getStub($name);

        $this->files->put($path, $stub);

        $this->info("DTO {$name} успешно создан.");
        return true;
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }

    protected function getStub($name)
    {
        // Простая заготовка для DTO-класса
        return <<<EOT
<?php

namespace App\DTOs;

class {$name}
{
    // Добавьте необходимые поля

    public function __construct()
    {
        // Логика конструктора
    }

    public static function fromRequest(\$request): {$name}
    {
        return new self();
    }
}

EOT;
    }
}
