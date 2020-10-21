<?php


namespace henzeb\ComposerLink\Manager;


use Composer\Composer;
use Composer\Config;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Package\Link;
use Composer\Util\Filesystem;


class ComposerManager
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function getName(): string
    {
        return $this->composer->getPackage()->getName();
    }

    public function getExtra(string $key, string $default = ''): string {
        $extra = $this->composer->getPackage()->getExtra();

        return $extra[$key]??$default;
    }

    public function isPackageInstalled(string $name): bool
    {
        $package = $this->getPackage($name);
        return $package ? Filesystem::isLocalPath(
            $this->getVendorDir() . $package->getTarget()
        ) : false;
    }

    /**
     * @return array
     */
    private function getAllRequires(): array
    {
        return $this->composer->getPackage()->getRequires()
            + $this->composer->getPackage()->getDevRequires();
    }

    public function getVendorDir(): string
    {
        return $this->composer->getConfig()->get('vendor-dir') . DIRECTORY_SEPARATOR;
    }

    public function getPackage(string $name): ?Link
    {
        $requires = $this->getAllRequires();

        return $requires[$name] ?? null;
    }

    public function getComposerManagerFrom(string $path): self
    {
        return new ComposerManager(
            (new Factory)->createComposer(
            $this->io,
            $path . DIRECTORY_SEPARATOR . 'composer.json'
        ), $this->io);

    }

    public function getIO(): IOInterface
    {
        return $this->io;
    }


}