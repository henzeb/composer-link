<?php

namespace henzeb\ComposerLink\Command;

use Composer\Command\BaseCommand;
use henzeb\ComposerLink\Command\Interpreter\Interpreter;
use henzeb\ComposerLink\Filesystem\Filesystem;
use henzeb\ComposerLink\Manager\LinkManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LinkBaseCommand extends BaseCommand
{

    /**
     * @var LinkManager
     */
    private LinkManager $linkManager;


    public function __construct(LinkManager $linkManager)
    {
        parent::__construct();

        $this->linkManager = $linkManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $strategy = $this->getInterpreter()->interpret($input);

        if ($strategy) {
            return $strategy->execute($this->linkManager, $input, $output);
        }
    }

    private function getInterpreter(): Interpreter
    {
        return new Interpreter(
            ...$this->getCommandStrategies()
        );
    }

    abstract public function getCommandStrategies(): array;
}