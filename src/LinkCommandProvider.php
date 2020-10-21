<?php


namespace henzeb\ComposerLink;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider;
use henzeb\ComposerLink\Command\LinkCommand;
use henzeb\ComposerLink\Command\UnlinkCommand;
use henzeb\ComposerLink\Manager\LinkManager;

class LinkCommandProvider implements CommandProvider
{


    private LinkManager $linkManager;


    public function __construct($linkManager)
    {
        $this->linkManager = $linkManager['plugin']->getLinkmanager();
    }

    /**
     * @return array|BaseCommand[]
     */
    public function getCommands()
    {
        return [
            new LinkCommand($this->linkManager),
            new UnlinkCommand($this->linkManager)
        ];
    }
}