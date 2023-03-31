<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected const CONFIGURATION_PATH = 'nfz';

    protected \Magento\Framework\DataObject $config;
    protected array $healthcheckUrl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        array $healthcheckUrl
    ) {
        $this->healthcheckUrl = $healthcheckUrl;

        parent::__construct($context);
    }

    public function getConfiguration($storeId = null): \Magento\Framework\DataObject
    {
        if (empty($this->config)) {
            $this->initConfig();
        }

        return $this->config;
    }

    public function getHealthcheckUrl(): array
    {
        return [];
    }

    protected function initConfig(): void
    {
        $configuration = $this->scopeConfig->getValue(self::CONFIGURATION_PATH);
        $configs = [];

        foreach ($configuration as $key => $config) {
            $this->setDefaultHealthcheckUrl($config, $key);
            $configs[$key] = $this->config = new \Magento\Framework\DataObject($config);
        }

        $this->config = new \Magento\Framework\DataObject($configs);
    }

    protected function setDefaultHealthcheckUrl(array &$config, string $key)
    {
        if (isset($config['healthcheck_url']) && !empty($config['healthcheck_url'])) {
            return;
        }

        if (empty($this->healthcheckUrl[$key])) {
            return;
        }

        $config['healthcheck_url'] = $this->healthcheckUrl[$key];
    }
}
