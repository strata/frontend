<?php

declare(strict_types=1);

namespace Strata\Frontend\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class ContentSchemaCommand extends Command
{
    const CONTENT_MODEL = 'content-model.yaml';

    protected static $defaultName = 'generate:schema';

    protected function configure()
    {
        $this
            ->setDescription('Generates content schema config files')
            ->setHelp('Generates or updates a content schema config files based on API inspection')
            ->addArgument('uri', InputArgument::REQUIRED, 'URI to API endpoint)')
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path where to store content model config files', './config/content')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $input->getArgument('uri');
        $path = $input->getOption('path');
        $helper = $this->getHelper('question');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        // @todo
        $question = new Question('Please enter the name of the bundle (AcmeDemoBundle) ', 'AcmeDemoBundle');
        $bundleName = $helper->ask($input, $output, $question);
        echo $bundleName;

        return Command::SUCCESS;
    }
}
