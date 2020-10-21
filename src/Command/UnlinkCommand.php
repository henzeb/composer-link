<?php

namespace henzeb\ComposerLink\Command;

use henzeb\ComposerLink\Command\Strategy\UnlinkFromConfigStrategy;
use henzeb\ComposerLink\Command\Strategy\UnlinkStrategy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UnlinkCommand extends LinkBaseCommand
{
    protected function configure()
    {
        $this->setName('unlink');
        $this->setDescription('Unlinks a local package');

        $this->setDefinition([
            new InputArgument('package', InputArgument::OPTIONAL, 'The package name you want to unlink'),
            new InputOption('remove', 'r', InputOption::VALUE_NONE, 'Remove from config'),
        ]);
    }


    public function getCommandStrategies(): array
    {
        return [
            new UnlinkFromConfigStrategy(),
            new UnlinkStrategy()
        ];
    }
}