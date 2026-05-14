<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Helper;

class Configuration
{
    public const XML_PATH_ELASTICSEARCH_HEALTHCHECK_URL = 'nfz/elasticsearch/healthcheck_url';
    public const XML_PATH_ELASTICSEARCH_LIMIT = 'nfz/elasticsearch/limit';

    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {}

    public function getHealthCheckUrl(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ELASTICSEARCH_HEALTHCHECK_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLimit(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ELASTICSEARCH_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
