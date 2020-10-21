<?php

namespace henzeb\ComposerLink\Command;

use henzeb\ComposerLink\Command\Strategy\CommandStrategy;
use henzeb\ComposerLink\Command\Strategy\LinkFromConfigStrategy;
use henzeb\ComposerLink\Command\Strategy\LinkStrategy;
use Symfony\Component\Console\Input\InputArgument;

class LinkCommand extends LinkBaseCommand
{
    protected function configure()
    {
        $this->setName('link');
        $this->setDescription('Links a package to a local package, for development purposes.');

        $this->setDefinition([
            new InputArgument('package', InputArgument::OPTIONAL, 'The path of the package you want to link to, or the package name of a previously linked package.'),
        ]);
    }

    /**
     * @return CommandStrategy[]
     */
    public function getCommandStrategies(): array
    {
        return [
            new LinkFromConfigStrategy(),
            new LinkStrategy(),
        ];
    }
}