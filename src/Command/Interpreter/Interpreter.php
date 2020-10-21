<?php

namespace henzeb\ComposerLink\Command\Interpreter;

use henzeb\ComposerLink\Command\Strategy\CommandStrategy;
use Symfony\Component\Console\Input\InputInterface;

class Interpreter
{
    /**
     * @var CommandStrategy[]
     */
    private $strategies;

    public function __construct(CommandStrategy ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public function interpret(InputInterface $input): ?CommandStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->satisfiedBy($input)) {
                return $strategy;
            }
        }

        return null;
    }
}