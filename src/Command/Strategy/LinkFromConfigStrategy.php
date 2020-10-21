<?php


namespace henzeb\ComposerLink\Command\Strategy;


use Exception;
use henzeb\ComposerLink\Manager\LinkManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkFromConfigStrategy extends CommandStrategy
{

    public function satisfiedBy(InputInterface $input): bool
    {
        preg_match('/^[^\/]+\/[^\/]+$/', $input->getArgument('package') ?? '', $output_array);

        return !empty($output_array);
    }

    public function execute(LinkManager $linkManager, InputInterface $input, OutputInterface $output): int
    {
        $packageName = $input->getArgument('package');

        try {
            if ($packageName) {
                $linkManager->linkPackageFromConfig($packageName);
                return 0;
            }

            return 0;

        } catch (Exception $e) {

            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return (int)$e->getCode();
        }
    }
}