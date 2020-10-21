<?php

namespace henzeb\ComposerLink\Command\Strategy;

use henzeb\ComposerLink\Manager\LinkManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandStrategy
{


    public abstract function satisfiedBy(InputInterface $input): bool;

    public abstract function execute(LinkManager $linkManager, InputInterface $input, OutputInterface $output): int;
}