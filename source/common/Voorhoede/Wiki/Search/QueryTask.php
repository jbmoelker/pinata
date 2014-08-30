<?php

namespace Voorhoede\Wiki\Search;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Voorhoede\Wiki\Search\SearchQuery;

class QueryTask extends Command
{
    protected function configure()
    {
        $this
            ->setName('wiki:query')
            ->setDescription('Perform full-text search within wiki.')
            ->addArgument(
                'query',
                InputArgument::REQUIRED,
                'What are you looking for?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getArgument('query');

        $output->writeln('<info>Search wiki for `' .$query. '`</info>');

        $hits = SearchQuery::find($query);

        foreach ($hits as $hit) {
            $output->writeln('- '. $hit->title);
            $output->writeln('\t score'. $hit->score);
        }

        $output->writeln('<info>Search complete</info>');
    }
}
