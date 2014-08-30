<?php

namespace Linguistadores\Pinata\Translator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Linguistadores\Pinata\Translator\Translator;

class TranslateTask extends Command
{
    protected function configure()
    {
        $this
            ->setName('pinata:translate')
            ->setDescription('Translate word from language to another')
            ->addArgument(
                'sourceLang',
                InputArgument::REQUIRED,
                'What language do you want to translate from? (sourceLang)'
            )
            ->addArgument(
                'targetLang',
                InputArgument::REQUIRED,
                'What language do you want to translate to? (targetLang)'
            )
            ->addArgument(
                'word',
                InputArgument::REQUIRED,
                'What\'s the word you want to translate?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceLang = $input->getArgument('sourceLang');
        $targetLang = $input->getArgument('targetLang');
        $word = $input->getArgument('word');

        $output->writeln(sprintf('Translating `%s` from %s to %s', $word, strtoupper($sourceLang), strtoupper($targetLang) ));

        $translator = new Translator($sourceLang, $targetLang);
        $translations = $translator->translate($word);

        $output->writeln(sprintf('Translations: %s', implode(', ', $translations) ));
    }
}
