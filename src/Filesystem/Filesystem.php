<?php
namespace henzeb\ComposerLink\Filesystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends SymfonyFilesystem
{
    public function makePathRelative($endPath, $startPath): string
    {
        if(!$this->isAbsolutePath($endPath)) {
            $endPath = $this->readlink($endPath, true);
        }

        if(!$this->isAbsolutePath($startPath)) {
            $startPath = $this->readlink($startPath, true);
        }

        return parent::makePathRelative($endPath, $startPath);
    }

    public function readFile(string $filename): string
    {
        $content = '';
        if (false === $content = @file_get_contents($filename)) {
            throw new IOException(sprintf('Failed to read file "%s".', $filename), 0, null, $filename);
        }

        return $content;
    }

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     *
     * @return string|null
     */
    public function readlink(string $path, bool $canonicalize = false)
    {
        if (!$canonicalize && !is_link($path)) {
            return null;
        }

        if ($canonicalize) {
            if (!$this->exists($path)) {
                return null;
            }

            if ('\\' === \DIRECTORY_SEPARATOR) {
                $path = readlink($path);
            }

            return realpath($path);
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            return realpath($path);
        }

        return readlink($path);
    }
}