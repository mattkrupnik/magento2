<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Model\Cron;

use Magento\Setup\Console\Command\UpgradeCommand;
use Symfony\Component\Console\Output\StreamOutput;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory class to create jobs
 */
class JobFactory
{
    /**
     * Name of jobs
     */
    const NAME_UPGRADE = 'setup:upgrade';

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Create job instance.
     *
     * @param string $name
     * @param array $params
     * @return AbstractJob
     * @throws \RuntimeException
     */
    public function create($name, array $params = [])
    {
        switch ($name) {
            case self::NAME_UPGRADE:
                return new JobUpgrade(
                    $this->serviceLocator->get('Magento\Setup\Console\Command\UpgradeCommand'),
                    $this->serviceLocator->get('Magento\Setup\Model\ObjectManagerProvider'),
                    $this->serviceLocator->get('Magento\Framework\App\MaintenanceMode'),
                    new MultipleStreamOutput(
                        [
                            fopen($this->serviceLocator->get('Magento\Setup\Model\Cron\Status')
                                ->getStatusFilePath(), 'a+'),
                            fopen($this->serviceLocator->get('Magento\Setup\Model\Cron\Status')
                                ->getLogFilePath(), 'a+'),
                        ]
                    ),
                    $this->serviceLocator->get('Magento\Setup\Model\Cron\Status'),
                    $name,
                    $params
                );
                break;
            default:
                throw new \RuntimeException(sprintf('"%s" job is not supported.', $name));
        }
    }
}
