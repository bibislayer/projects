<?php
// src/Acme/DemoBundle/Command/GreetCommand.php
namespace  SO\ScrapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scrap:dpstream')
            ->setDescription('Get movie name')
            ->addArgument('offset', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
            ->addArgument('total', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offset = $input->getArgument('offset');
        $limit = $input->getArgument('limit');
        $objScrap = $this->getContainer()->get('so_scrap.controller');
        $objScrap->setContainer($this->getContainer());
        $objScrap->searchDpStreamAction($limit);
    }
}