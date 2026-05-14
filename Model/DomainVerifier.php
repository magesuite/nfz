<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Model;

class DomainVerifier
{
    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        protected \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {}

    public function verify(): array
    {
        $domains = $this->getDomainList();
        $results = [];

        foreach ($domains as $domain) {
            $results[$domain] = $this->checkSsl($domain);
        }

        return $results;
    }

    protected function getDomainList(): array
    {
        $domains = [];

        foreach ($this->storeManager->getStores() as $store) {
            if (!$store->getIsActive()) {
                continue;
            }

            $baseUrl = (string)$this->scopeConfig->getValue(
                \Magento\Store\Model\Store::XML_PATH_SECURE_BASE_URL,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            $host = $this->extractHost($baseUrl);

            if ($host === null) {
                continue;
            }

            $domains[] = $host;
        }

        return array_unique($domains);
    }

    protected function extractHost(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (!is_string($host) || $host === '') {
            return null;
        }

        return $host;
    }

    protected function checkSsl(string $domain): bool
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => true,
                'verify_peer_name' => true,
                'SNI_enabled' => true
            ]
        ]);

        $client = @stream_socket_client(
            "ssl://{$domain}:443",
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$client) {
            return false;
        }

        try {
            $params = stream_context_get_params($client);
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);

            if (!is_array($cert) || !isset($cert['validTo_time_t'])) {
                return false;
            }

            $validTo = (int)$cert['validTo_time_t'];
            $daysLeft = (int)floor(($validTo - time()) / 86400);

            return $daysLeft >= 0;
        } finally {
            fclose($client);
        }
    }
}
