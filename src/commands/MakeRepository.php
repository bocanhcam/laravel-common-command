<?php

namespace Hora\LaravelCommonCommand\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class MakeRepository extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * @var string
     */
    protected $signature = 'make:repository {name} {--prefix=} {--template}';

    /**
     * @var string
     */
    protected $description = 'Create a Repository and Interface Class';

    /**
     * @return bool|void|null
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $paths = $this->getPaths();

        foreach ($paths as $key => $path) {
            if ($this->alreadyRepositoryExists($key, $this->getNameInput())) {
                $this->error($this->type . ' ' . ucfirst($key) . ' already exists!');
                continue;
            }
            $name = $this->qualifyClasses($key, $this->getNameInput());

            $this->makeDirectory($path);

            $this->files->put($path, $this->buildRepository($key, $name));

            $this->info($this->type . ' ' . ucfirst($key) . ' created successfully.');
        }
    }

    /**
     * @return string|void
     */
    protected function getStub()
    {
        //
    }

    /**
     * @return array|string[]
     */
    protected function getStubs(): array
    {
        return [
            'interface' => __DIR__ . '/../stubs/interface.stub',
            'repository' => __DIR__ . '/../stubs/repository.stub',
        ];
    }

    /**
     * @return array|string[]
     */
    protected function getStubsWithTemplate(): array
    {
        return [
            'interface' => __DIR__ . '/../stubs/interface_template.stub',
            'repository' => __DIR__ . '/../stubs/repository_template.stub',
        ];
    }

    /**
     * @return string|string[]
     */
    protected function getPaths()
    {
        $name = str_replace(
            '\\',
            '/',
            $this->getNameInput()
        );

        $class_name = Str::afterLast($name, '/');

        if ($this->option('prefix')){
            $class_name_prefix = $this->option('prefix') . $class_name;
            $name = str_replace($class_name, $class_name_prefix, $name);
        }

        $repository = config('laravel-common-command.repositories');
        $interface = config('laravel-common-command.interfaces');

        $interface_path = app_path() . "/$interface/{$class_name}RepositoryInterface.php";
        $repository_path = app_path() . "/$repository/{$name}Repository.php";

        return [
            'interface' => $interface_path,
            'repository'  => $repository_path
        ];

        return app_path() . "/" . "{$name}.php";
    }

    /**
     * @param $type
     * @param $name
     * @return string|string[]
     * @throws FileNotFoundException
     */
    protected function buildRepository($type, $name)
    {
        $stubs = $this->option('template') ? $this->getStubsWithTemplate() : $this->getStubs();

        $stub = $this->files->get($stubs[$type]);

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $stub = $this->replaceModel($stub, $name);

        $stub = $this->replaceVariable($stub, $name);

        $stub = $this->replaceInterfaceNamespace($stub);

        $stub = $this->replacePrefix($stub);

        return $stub;
    }

    /**
     * @param $type
     * @param $name
     * @return string
     */
    protected function qualifyClasses($type, $name): string
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        if ($type === 'interface'){
            $name = Str::afterLast($name, '\\');

            $rootNamespace = $this->rootNamespace() . config('laravel-common-command.interfaces');
        }else{
            $rootNamespace = $this->rootNamespace() . config('laravel-common-command.repositories');
        }

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        //$name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * @param $stub
     * @param $name
     * @return string|string[]
     */
    protected function replaceModel($stub, $name)
    {
        $model = str_replace([$this->getNamespace($name) . '\\', 'Repository'], '', $name);

        return str_replace('DummyModel', $model, $stub);
    }

    /**
     * @param $stub
     * @return string|string[]
     */
    protected function replaceInterfaceNamespace(&$stub)
    {
        $namespace = $this->rootNamespace() . config('laravel-common-command.interfaces');

        return str_replace('DummyInterfaceNamespace', $namespace, $stub);
    }

    /**
     * @param $stub
     * @return string|string[]
     */
    protected function replacePrefix(&$stub)
    {
        return str_replace('PrefixRepository', $this->option('prefix'), $stub);
    }

    /**
     * @param $stub
     * @param $name
     * @return string|string[]
     */
    protected function replaceVariable($stub, $name)
    {
        $model = str_replace([$this->getNamespace($name) . '\\', 'Repository'], '', $name);
        $variable = Str::snake($model);

        return str_replace('DummyVariable', $variable, $stub);
    }

    /**
     * @param $type
     * @param $input_name
     * @return bool
     */
    protected function alreadyRepositoryExists($type, $input_name): bool
    {
        $repository = config('laravel-common-command.repositories');
        $interface = config('laravel-common-command.interfaces');

        $input_name = str_replace('\\', '/', $input_name);

        if ($type === 'interface') {
            $name = Str::afterLast($input_name, '/');
            $interface_path = "$interface/$name"."RepositoryInterface";
            $class_name = $interface_path;
        } else {
            if (Str::contains($input_name, '/')){
                $name_dir = Str::beforeLast($input_name, '/');
            }else{
                $name_dir = "";
            }

            $name = Str::afterLast($input_name, '/');
            if ($this->option('prefix')){
                $repository_path = "/$repository/" . $name_dir . "/" . ucfirst($this->option('prefix')).$name."Repository";
            }else{
                $repository_path = "/$repository/". $name_dir . "/".$name."Repository";
            }
            $class_name = str_replace('//', '/', $repository_path);
        }

        return $this->files->exists($this->getPath($this->qualifyClass($class_name)));
    }

}
