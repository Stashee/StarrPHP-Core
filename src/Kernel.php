<?php

namespace StarrPHP\Core;

use ReflectionException;

class Kernel
{
    private Router $router;
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function scanControllers(string $directory, ?string $cacheFile = null, ?string $baseDir = null): void
    {
        if ($cacheFile !== null && file_exists($cacheFile)) {
            $this->router->setRoutes(require $cacheFile);
            return;
        }

        $files = glob(rtrim($directory, '/') . '/*.php');
        $base = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $baseDir ?? dirname($directory));

        foreach ($files as $file) {
            $file = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
            $relative = str_replace([$base . DIRECTORY_SEPARATOR, '.php'], '', $file);
            $className = str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

            if (!class_exists($className)) {
                continue;
            }

            try {
                $this->router->registerController($className);
            } catch (ReflectionException $e) {
                error_log("Failed to register controller $className: " . $e->getMessage());
            }
        }
    }

    public function buildRouteCache(string $directory, string $cacheFile, ?string $baseDir = null): void
    {
        $this->scanControllers($directory, null, $baseDir);

        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $exported = var_export($this->router->getRoutes(), true);
        file_put_contents($cacheFile, "<?php return {$exported};\n");
    }

    public function handle(Request $request): void
    {
        $this->container->bindInstance(Request::class, $request);
        $this->router->resolve($request->uri, $request->method)->send();
    }
}