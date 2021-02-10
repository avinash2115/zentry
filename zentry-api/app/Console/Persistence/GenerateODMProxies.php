<?php

namespace App\Console\Commands\Persistence;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Tools\Console\MetadataFilter;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use LaravelDoctrine\ORM\Console\Command;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class GenerateODMProxies
 *
 * @package App\Console\Commands\Persistence
 */
class GenerateODMProxies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odm:generate:proxies
     {dest-path? : The path to generate your proxy classes. If none is provided, it will attempt to grab from configuration.}
    {-- filter=* : A string pattern used to match entities that should be processed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates proxy classes for document classes.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function handle(): void
    {
        $this->message($this->description);
        $dm = app()->make(DocumentManager::class);

        $metaData = (array)array_filter(
            $dm->getMetadataFactory()->getAllMetadata(),
            function (
                ClassMetadata $classMetadata
            ) {
                return !$classMetadata->isEmbeddedDocument && !$classMetadata->isMappedSuperclass && !$classMetadata->isQueryResultDocument;
            }
        );

        $filter = $this->option('filter');

        if (!is_array($filter) && !is_string($filter)) {
            throw new UnexpectedValueException('filter option can contain only array or string values');
        }

        $metaData = MetadataFilter::filter($metaData, $filter);

        // Process destination directory
        $destinationPath = $this->argument('dest-path');
        if ($destinationPath === null) {
            $destinationPath = $dm->getConfiguration()->getProxyDir();
        }

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0775, true)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $destinationPath));
        }

        $destinationPath = realpath($destinationPath);

        if (!is_string($destinationPath) || !file_exists($destinationPath)) {
            throw new InvalidArgumentException(
                sprintf("Proxies destination directory '<info>%s</info>' does not exist.", $destinationPath)
            );
        }

        if (!is_writable($destinationPath)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Proxies destination directory '<info>%s</info>' does not have write permissions.",
                    $destinationPath
                )
            );
        }

        if (count($metaData)) {
            // Generating Proxies
            $dm->getProxyFactory()->generateProxyClasses($metaData);

            // Outputting information message
            $this->message(
                PHP_EOL . sprintf('Proxy classes generated to "<info>%s</INFO>"', $destinationPath) . PHP_EOL
            );
        } else {
            $this->message('No Metadata Classes to process.' . PHP_EOL);
        }
    }
}
