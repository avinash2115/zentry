<?php

namespace App\Console;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Flusher;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class Command
 *
 * @package App\Console
 */
class Command extends IlluminateCommand
{
    public const OPTION_MEMORY = ' {--memory}';

    /**
     * @var ProgressBar | null
     */
    private ?ProgressBar $progress = null;

    /**
     * @inheritDoc
     */
    public function info($string, $verbosity = null)
    {
        parent::info("\n[{$this->name}] {$string}", $verbosity);
    }

    /**
     * @inheritDoc
     */
    public function alert($string)
    {
        parent::alert("\n[{$this->name}] {$string}");
    }

    /**
     * @inheritDoc
     */
    public function warn($string, $verbosity = null)
    {
        parent::warn("\n[{$this->name}] {$string}", $verbosity);
    }

    /**
     * @param int $amount
     */
    final public function progressStart(int $amount): void
    {
        if ($amount > 0) {
            $this->progress = $this->output->createProgressBar($amount);
            $this->_progress()->start();
        }
    }

    /**
     *
     */
    final public function progressAdvance(): void
    {
        $this->_progress()->advance();
    }

    /**
     *
     */
    final public function progressFinish(): void
    {
        try {
            $this->_progress()->finish();
        } catch (PropertyNotInit $exception) {
        }
    }

    /**
     * @return ProgressBar
     * @throws PropertyNotInit
     */
    private function _progress(): ProgressBar
    {
        if (!$this->progress instanceof ProgressBar) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->progress;
    }

    /**
     *
     */
    final public function _handle(callable $closure, bool $clear = true): void
    {
        $this->info('Started ... ');

        $closure();

        if ($clear) {
            Flusher::clear();
        }

        $this->info('Finished!');
    }

    /**
     *
     */
    final protected function memoryConsumption(): void
    {
        if (!$this->option('memory')) {
            return;
        }

        $this->warn('Memory consumption:' . readableBytes(memory_get_usage(true)));
    }
}
