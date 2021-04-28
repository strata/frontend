<?php

declare(strict_types=1);

namespace Strata\Frontend\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ContentTypeCommand extends Command
{
    const CONTENT_TYPE = '%s.yaml';

    protected static $defaultName = 'generate:type';

    protected function configure()
    {
        $this
            ->setDescription('Generates content type config files')
            ->setHelp('Generates or updates a content type config files based on API inspection')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the content type to generate (e.g. news)')
            ->addArgument('uri', InputArgument::OPTIONAL, 'URI to API endpoint)')
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path where to store content model config files', './config/content')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $uri = $input->getArgument('uri');
        $path = $input->getOption('path');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        // @todo

        return Command::SUCCESS;
    }
}
