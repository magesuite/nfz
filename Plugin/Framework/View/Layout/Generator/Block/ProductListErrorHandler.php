<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Plugin\Framework\View\Layout\Generator\Block;

class ProductListErrorHandler
{
    public function __construct(
        protected \Magento\Framework\App\ResponseInterface $response,
        protected array $blockList = []
    ) {}

    public function aroundCreateBlock(
        \Magento\Framework\View\Layout\Generator\Block $subject,
        callable $proceed,
        $block,
        $name,
        array $arguments = []
    ) {
        if (!in_array($name, $this->blockList)) {
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
