<?php


namespace henzeb\ComposerLink\Manager;


use Exception;
use henzeb\ComposerLink\Filesystem\Filesystem;
use henzeb\ComposerLink\Package\PackageLink;

class LinkManager
{
    private static ?self $instance = null;
    private Filesystem $filesystem;

    private ComposerManager $composerManager;

    private ConfigManager $configManager;

    public function __construct(
        Filesystem $filesystem,
        ConfigManager $configManager,
        ComposerManager $composerManager
    )
    {
        $this->filesystem = $filesystem;
        $this->composerManager = $composerManager;
        $this->configManager = $configManager;
    }

    public static function getInstance(Filesystem $filesystem, ConfigManager $configManager, ComposerManager $composerManager): self
    {
        return self::$instance ?? new LinkManager($filesystem, $configManager, $composerManager);
    }

    public function getConfigManager(): ConfigManager
    {
        return $this->configManager;
    }

    /**
     * @param string $packageName
     * @param string $path
     * @return PackageLink
     * @throws Exception
     */
    public function link(string $packageName, string $path): PackageLink
    {
        $this->validate($packageName, $path);

        $targetDir = $this->getTargetDir($packageName);

        if ($this->filesystem()->exists($targetDir) && null === $this->filesystem()->readlink($targetDir)) {

            $this->removeExistingOldDir($targetDir);

            $this->filesystem()->rename($targetDir, $targetDir . '.old');
        }

        $this->filesystem()->symlink(
            $this->filesystem->makePathRelative($path, $targetDir . '/../'),
            $targetDir
        );

        return new PackageLink($packageName, $path);
    }

    /**
     * @param string $path
     * @return PackageLink
     * @throws Exception
     */
    public function linkFromPath(string $path): PackageLink
    {
        $package = $this->getComposerManager()->getComposerManagerFrom($path);
        return $this->link($package->getName(), $path);
    }

    /**
     * @param string $packageName
     * @throws Exception
     */
    public function linkPackageFromConfig(string $packageName)
    {
        $io = $this->getComposerManager()->getIO();
        $io->write('<info>Linking Package</info>');

        $packageLinks = $this->getConfigManager()->getPackageLinks();
        if (!isset($packageLinks[$packageName])) {
            throw new Exception('Package ' . $packageName . ' is not configured as a linkable package.', 5);
        }
        $this->linkPackageObject($packageLinks[$packageName]);

        $this->getComposerManager()
            ->getIO()
            ->write('linked package: <info>' . $packageLinks[$packageName]->getName() . '</info>');

    }

    /**
     * @param PackageLink $packageLink
     * @return PackageLink
     * @throws Exception
     */
    public function linkPackageObject(PackageLink $packageLink): PackageLink
    {
        return $this->link($packageLink->getName(), $packageLink->getPath());
    }


    public function linkAllFromConfig()
    {
        $this->linkPackages(...array_values($this->getConfigManager()->getPackageLinks()));
    }

    public function linkPackages(PackageLink ...$packageLinks)
    {
        $io = $this->getComposerManager()->getIO();

        $io->write('<info>Linking Packages</info>');

        if (empty($packageLinks)) {
            $io->write('Nothing to link');
        }

        try {
            foreach ($packageLinks as $packageLink) {
                /**
                 * @var PackageLink $packageLink
                 */
                $this->linkPackageObject($packageLink);
                $this->getComposerManager()
                    ->getIO()
                    ->write('linked package: <info>' . $packageLink->getName() . '</info>');
            }
        } catch (Exception $e) {
            $io->writeError('<error>' . $e->getMessage() . '</error>');
        }
    }

    public function unlink(string $packageName): void
    {
        $targetDir = $this->getTargetDir($packageName);

        if (null !== $this->filesystem()->readlink($targetDir)) {
            $this->filesystem()->remove($targetDir);
        }

        if ($this->filesystem()->exists($targetDir . '.old')) {
            $this->filesystem()->rename($targetDir . '.old', $targetDir);
        }
    }

    public function unlinkPackages(PackageLink ...$packageLinks)
    {
        $io = $this->getComposerManager()->getIO();

        $io->write('<info>Unlinking Packages</info>');

        if (empty($packageLinks)) {
            $io->write('Nothing to link');
        }

        try {
            foreach ($packageLinks as $packageLink) {
                /**
                 * @var PackageLink $packageLink
                 */
                $this->unlink($packageLink->getName());
                $this->getComposerManager()
                    ->getIO()
                    ->write('unlinked package: <info>' . $packageLink->getName() . '</info>');
            }
        } catch (Exception $e) {
            $io->writeError('<error>' . $e->getMessage() . '</error>');
        }
    }

    public function unlinkFromPath(string $path): PackageLink
    {
        $package = $this->getComposerManager()->getComposerManagerFrom($path);
        $this->unlink($package->getName());

        return new PackageLink($package->getName(), $path);
    }

    public function unlinkAllFromConfig()
    {
        $this->unlinkPackages(...array_values($this->configManager->getPackageLinks()));
    }

    private function getTargetDir(string $packageName): string
    {
        return $this->getComposerManager()
                ->getVendorDir()
            . $this->getComposerManager()
                ->getPackage($packageName)
                ->getTarget();
    }

    /**
     * @param string $packageName
     * @param string $path
     * @throws Exception
     */
    private function validate(string $packageName, string $path)
    {
        if (!$this->getComposerManager()->isPackageInstalled($packageName)) {
            throw new Exception('Package ' . $packageName . ' is not installed. Please require this package first.', 1);
        }

        if (!$this->filesystem()->exists($path . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new Exception('Path ' . $path . ' does not exist or not lead to a package', 2);
        }

        $packageConfig = $this->composerManager->getComposerManagerFrom($path);

        if ($packageConfig->getName() !== $packageName) {
            throw new Exception('Path ' . $path . ' does not contain a package with the name ' . $packageName, 3);
        }
    }

    /**
     * @param string $targetDir
     */
    private function removeExistingOldDir(string $targetDir): void
    {
        if ($this->filesystem()->exists($targetDir . '.old')) {
            $this->filesystem()->remove($targetDir . '.old');
        }
    }

    /**
     * @return ComposerManager
     */
    private function getComposerManager(): ComposerManager
    {
        return $this->composerManager;
    }

    /**
     * @return Filesystem
     */
    private function filesystem(): Filesystem
    {
        return $this->filesystem;
    }
}