<?php

namespace Livewire\Commands;

use WpStarter\Support\Arr;
use WpStarter\Support\Str;
use WpStarter\Support\Facades\File;
use function Livewire\str;

#[\AllowDynamicProperties]
class ComponentParser
{
    protected $appPath;
    protected $viewPath;
    protected $component;
    protected $componentClass;
    protected $directories;

    public function __construct($classNamespace, $viewPath, $rawCommand, $stubSubDirectory = '')
    {

        $this->baseClassNamespace = $classNamespace;
        $this->baseTestNamespace = 'Tests\Feature\Livewire';

        $classPath = static::generatePathFromNamespace($classNamespace);
        $testPath = static::generateTestPathFromNamespace($this->baseTestNamespace);

        $this->baseClassPath = rtrim($classPath, DIRECTORY_SEPARATOR).'/';
        $this->baseViewPath = rtrim($viewPath, DIRECTORY_SEPARATOR).'/';
        $this->baseTestPath = rtrim($testPath, DIRECTORY_SEPARATOR).'/';

        if(! empty($stubSubDirectory) && str($stubSubDirectory)->startsWith('..')) {
            $this->stubDirectory = rtrim(str($stubSubDirectory)->replaceFirst('..' . DIRECTORY_SEPARATOR, ''), DIRECTORY_SEPARATOR).'/';
        } else {
            $this->stubDirectory = rtrim('stubs'.DIRECTORY_SEPARATOR.$stubSubDirectory, DIRECTORY_SEPARATOR).'/';
        }

        $directories = preg_split('/[.\/(\\\\)]+/', $rawCommand);

        $camelCase = str(array_pop($directories))->camel();
        $kebabCase = str($camelCase)->kebab();

        $this->component = $kebabCase;
        $this->componentClass = str($this->component)->studly();

        $this->directories = array_map([Str::class, 'studly'], $directories);
    }

    public function component()
    {
        return $this->component;
    }

    public function classPath()
    {
        return $this->baseClassPath.ws_collect()
            ->concat($this->directories)
            ->push($this->classFile())
            ->implode('/');
    }

    public function relativeClassPath() : string
    {
        return str($this->classPath())->replaceFirst(ws_base_path().DIRECTORY_SEPARATOR, '');
    }

    public function classFile()
    {
        return $this->componentClass.'.php';
    }

    public function classNamespace()
    {
        return empty($this->directories)
            ? $this->baseClassNamespace
            : $this->baseClassNamespace.'\\'.ws_collect()
                ->concat($this->directories)
                ->map([Str::class, 'studly'])
                ->implode('\\');
    }

    public function className()
    {
        return $this->componentClass;
    }

    public function classContents($inline = false)
    {
        $stubName = $inline ? 'livewire.inline.stub' : 'livewire.stub';

        if (File::exists($stubPath = ws_base_path($this->stubDirectory.$stubName))) {
            $template = file_get_contents($stubPath);
        } else {
            $template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.$stubName);
        }

        if ($inline) {
            $template = preg_replace('/\[quote\]/', $this->wisdomOfTheTao(), $template);
        }

        return preg_replace(
            ['/\[namespace\]/', '/\[class\]/', '/\[view\]/'],
            [$this->classNamespace(), $this->className(), $this->viewName()],
            $template
        );
    }

    public function viewPath()
    {
        return $this->baseViewPath.ws_collect()
            ->concat($this->directories)
            ->map([Str::class, 'kebab'])
            ->push($this->viewFile())
            ->implode(DIRECTORY_SEPARATOR);
    }

    public function relativeViewPath() : string
    {
        return str($this->viewPath())->replaceFirst(ws_base_path().'/', '');
    }

    public function viewFile()
    {
        return $this->component.'.blade.php';
    }

    public function viewName()
    {
        return ws_collect()
            ->when(ws_config('livewire.view_path') != ws_resource_path(), function ($collection) {
                return $collection->concat(explode('/',str($this->baseViewPath)->after(ws_resource_path('views'))));
            })
            ->filter()
            ->concat($this->directories)
            ->map([Str::class, 'kebab'])
            ->push($this->component)
            ->implode('.');
    }

    public function viewContents()
    {
        if( ! File::exists($stubPath = ws_base_path($this->stubDirectory.'livewire.view.stub'))) {
            $stubPath = __DIR__.DIRECTORY_SEPARATOR.'livewire.view.stub';
        }

        return preg_replace(
            '/\[quote\]/',
            $this->wisdomOfTheTao(),
            file_get_contents($stubPath)
        );
    }

    public function testNamespace()
    {
        return empty($this->directories)
            ? $this->baseTestNamespace
            : $this->baseTestNamespace.'\\'.ws_collect()
                ->concat($this->directories)
                ->map([Str::class, 'studly'])
                ->implode('\\');
    }

    public function testClassName()
    {
        return $this->componentClass.'Test';
    }

    public function testFile()
    {
        return $this->componentClass.'Test.php';
    }

    public function testPath()
    {
        return $this->baseTestPath.ws_collect()
        ->concat($this->directories)
        ->push($this->testFile())
        ->implode('/');
    }

    public function relativeTestPath() : string
    {
        return str($this->testPath())->replaceFirst(ws_base_path().'/', '');
    }

    public function testContents()
    {
        $stubName = 'livewire.test.stub';

        if(File::exists($stubPath = ws_base_path($this->stubDirectory.$stubName))) {
            $template = file_get_contents($stubPath);
        } else {
            $template = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.$stubName);
        }

        return preg_replace(
            ['/\[testnamespace\]/', '/\[classwithnamespace\]/', '/\[testclass\]/', '/\[class\]/'],
            [$this->testNamespace(), $this->classNamespace() . '\\' . $this->className(), $this->testClassName(), $this->className()],
            $template
        );
    }

    public function wisdomOfTheTao()
    {
        $wisdom = require __DIR__.DIRECTORY_SEPARATOR.'the-tao.php';

        return Arr::random($wisdom);
    }

    public static function generatePathFromNamespace($namespace)
    {
        $name = str($namespace)->finish('\\')->replaceFirst(ws_app()->getNamespace(), '');
        return ws_app('path').'/'.str_replace('\\', '/', $name);
    }

    public static function generateTestPathFromNamespace($namespace)
    {
        return str(ws_base_path($namespace))
            ->replace('\\', '/', $namespace)
            ->replaceFirst('T', 't');
    }
}
