<?php


namespace henzeb\ComposerLink\Manager;


use henzeb\ComposerLink\Filesystem\Filesystem;
use henzeb\ComposerLink\Package\PackageLink;

class ConfigManager
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;
    private array $json;
    private string $path;

    public function __construct(Filesystem $filesystem, string $path = './linked.json')
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->loadConfig();
    }

    /**
     * @return Filesystem
     */
    public function filesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function addPackage(PackageLink $packageLink)
    {
        $this->json['packages'][$packageLink->getName()] = $packageLink->getPath();
    }

    public function removePackage(string $packageName)
    {
        unset($this->json['packages'][$packageName]);
    }

    public function getPackageLinks(): array
    {
        $packages = $this->json['packages'] ?? [];
        array_walk($packages,
            function (&$value, $key) {
                $value = new PackageLink($key, $value);
            });
        return $packages;
    }

    private function loadConfig(): array
    {
        return $this->json = $this->json ?? $this->getConfigFromFile();
    }

    private function getConfigFromFile(): array
    {
        if ($this->filesystem()->exists($this->getConfigPath())) {

            return json_decode(
                $this->filesystem()->readFile(
                    $this->getConfigPath()
                ),
                true
            );
        }
        return [];
    }

    private function saveToFile()
    {
        if (count($this->loadConfig()) > 0) {
            $this->filesystem()->dumpFile($this->getConfigPath(), json_encode($this->loadConfig(), JSON_PRETTY_PRINT));
        }
    }

    private function getConfigPath(): string
    {
        return $this->path;
    }

    public function __destruct()
    {
        $this->saveToFile();
    }
}