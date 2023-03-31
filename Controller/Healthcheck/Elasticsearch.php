<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Controller\Healthcheck;

class Elasticsearch extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();

        return $this->resultPageFactory->create();
    }
}
