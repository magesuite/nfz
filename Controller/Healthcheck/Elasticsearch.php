<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Controller\Healthcheck;

class Elasticsearch implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {}

    public function execute(): \Magento\Framework\View\Result\Page
    {
        return $this->resultPageFactory->create();
    }
}
