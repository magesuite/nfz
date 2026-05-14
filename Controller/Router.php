<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    public function __construct(
        protected \Magento\Framework\App\ActionFactory $actionFactory,
        protected \MageSuite\Nfz\Helper\Configuration $configuration
    ) {}

    public function match(
        \Magento\Framework\App\RequestInterface $request
    ): ?\Magento\Framework\App\ActionInterface
    {
        $requestUrl = trim($request->getPathInfo(), '/');
        $configHealthcheckUrl = trim($this->configuration->getHealthcheckUrl(), '/');

        if (empty($configHealthcheckUrl) || $requestUrl !== $configHealthcheckUrl) {
            return null;
        }

        $request
            ->setModuleName('nfz')
            ->setControllerName('healthcheck')
            ->setActionName('elasticsearch');

        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class,
            ['request' => $request]
        );
    }
}
