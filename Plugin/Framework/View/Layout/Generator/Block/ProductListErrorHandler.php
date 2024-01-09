<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Plugin\Framework\View\Layout\Generator\Block;

class ProductListErrorHandler
{
    protected const PRODUCT_LIST_BLOCK_NAME = 'nfz.products.list';

    protected \Magento\Framework\App\ResponseInterface $response;

    public function __construct(\Magento\Framework\App\ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function aroundCreateBlock(
        \Magento\Framework\View\Layout\Generator\Block $subject,
        callable $proceed,
        $block,
        $name,
        array $arguments = []
    ) {
        if ($name != self::PRODUCT_LIST_BLOCK_NAME) {
            return $proceed($block, $name, $arguments);
        }

        try {
            return $proceed($block, $name, $arguments);
        } catch (\Exception $e) {
            $this->response->setHttpResponseCode(500);
            throw $e;
        }
    }
}
