<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 18:50
 */

namespace App\Infrastructure\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class OutputLogger extends SymfonyStyle
{

    private $logger;
    private $textPassedToWritelnDirectly;

    public function __construct(InputInterface $input, OutputInterface $output, LoggerInterface $logger) {
        parent::__construct($input, $output);
        $this->logger = $logger;
        $this->setTextLikePassedToWritelnDirectly();
    }

    private function setTextLikePassedToWritelnDirectly()
    {
        $this->textPassedToWritelnDirectly = true;
    }

    public function title($message) {
        $this->setTextLikePassedToWritelnIndirectly();
        parent::title($message);
        $this->logger->info($message);
    }

    private function setTextLikePassedToWritelnIndirectly()
    {
        $this->textPassedToWritelnDirectly = false;
    }

    public function success($message) {
        $this->setTextLikePassedToWritelnIndirectly();
        parent::success($message);
        $this->logger->info($message);
    }

    public function error($message) {
        $this->setTextLikePassedToWritelnIndirectly();
        parent::error($message);
        $this->logger->error($message);
    }

    public function note($message) {
        $this->setTextLikePassedToWritelnIndirectly();
        parent::note($message);
        $this->logger->info($message);
    }

    public function section($message) {
        $this->setTextLikePassedToWritelnIndirectly();
        parent::section($message);
        $this->logger->info($message);
    }

    public function writeln($message, $type = self::OUTPUT_NORMAL) {
        parent::writeln($message, $type);

        if ($this->isTextPassedToWritelnDirectly()) {
            $this->logger->info($message);
        }

        $this->setTextLikePassedToWritelnDirectly();
    }

    private function isTextPassedToWritelnDirectly()
    {
        return $this->textPassedToWritelnDirectly;
    }

}