<?php

namespace Voorhoede\Wiki\Search;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Voorhoede\Wiki\Search\SearchIndex;

class IndexTask extends Command
{
    protected function configure()
    {
        $this
            ->setName('wiki:index')
            ->setDescription('Generate search index for all wiki articles.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of article to index'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        // to do: if name, only update index of one specific article?

        $searchIndex = new SearchIndex($output);
        $searchIndex->create();

        $output->writeln('<info>Search index generated successfully.</info>');
    }
}
