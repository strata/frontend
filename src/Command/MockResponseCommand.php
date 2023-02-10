<?php

declare(strict_types=1);

namespace Strata\Frontend\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class MockResponseCommand extends Command
{
    protected static $defaultName = 'generate:mock-response';

    protected function configure()
    {
        $this
            ->setDescription('Generates mock response files')
            ->setHelp('Generates a response file and info PHP file for a mock response for unit testing. For file example.json it saves example.json and example.json.info.php files for use with Strata\Data\Http\Response\MockResponseFromFile')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to mock response file (e.g. example.json)')
            ->addOption('response', 'r', InputOption::VALUE_NONE, 'Only create the mock response file (not the PHP info file)')
            ->addOption('info', 'i', InputOption::VALUE_NONE, 'Only create the info PHP file (not the response file)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Whether to force creation of mock response files, even if they already exist')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $force = $input->getOption('force');
        $infoOnly = $input->getOption('info');
        $responseOnly = $input->getOption('response');

        $filesystem = new Filesystem();

        if (!$infoOnly) {
            if (!$force && $filesystem->exists($file)) {
                $output->writeln(sprintf('<comment>Mock response file %s already exists</comment>', $file));
                return Command::FAILURE;
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'json':
                    $content = '{"name": "Test"}';
                    break;
                case 'html':
                    $content = '<h1>Test</h1>';
                    break;
                default:
                    $content = 'Test';
            }
            $filesystem->dumpFile($file, $content);
            $output->writeln(sprintf('<info>Mock response file created at %s</info>', $file));
        }

        if (!$responseOnly) {
            $infoFile = $file . '.info.php';
            if (!$force && $filesystem->exists($infoFile)) {
                $output->writeln(sprintf('<comment>Mock PHP info file %s already exists</comment>', $infoFile));
                return Command::FAILURE;
            }

            $infoContent = <<<'EOD'
<?php
$info = [
    'http_code' => 200,
    'response_headers' => [
    ]
];

EOD;

            $filesystem->dumpFile($infoFile, $infoContent);
            $output->writeln(sprintf('<info>Mock PHP info file created at %s</info>', $infoFile));
        }

        return Command::SUCCESS;
    }
}
