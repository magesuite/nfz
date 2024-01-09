<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Test\Integration\Controller\Healthcheck;

class ElasticsearchTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\HTTP\Client\Curl $curlClient = null;

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoConfigFixture default/nfz/elasticsearch/limit 10
     */
    public function testElasticsearchHealthcheckOk(): void
    {
        $this->dispatch('healthcheck/elasticsearch');
        ob_end_clean();

        $this->assertStringContainsString('Simple Product', $this->getResponse()->getBody());
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }
    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/nfz/elasticsearch/limit 10
     */
    public function testElasticsearchHealthcheckError(): void
    {
        try {
            $this->dispatch('healthcheck/elasticsearch');
        } catch (\Exception $e) {}  // phpcs:ignore

        $this->assertStringNotContainsString('Simple Product', $this->getResponse()->getBody());
        $this->assertEquals(500, $this->getResponse()->getHttpResponseCode());
    }
}
