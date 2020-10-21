<?php


namespace henzeb\ComposerLink\Command\Strategy;


use Exception;
use henzeb\ComposerLink\Filesystem\Filesystem;
use henzeb\ComposerLink\Manager\LinkManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnlinkFromConfigStrategy extends CommandStrategy
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
                $linkManager->unlink($packageName);
                return 0;
            }

            return 0;

        } catch (Exception $e) {

            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return (int)$e->getCode();
        }
    }
}