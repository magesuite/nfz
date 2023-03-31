<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Controller;

class NfzHealthcheckUrlRouter implements \Magento\Framework\App\RouterInterface
{
    protected \Magento\Framework\App\ActionFactory $actionFactory;

    protected \Magento\Framework\App\ResponseInterface $response;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \MageSuite\Nfz\Helper\Configuration $config,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->actionFactory = $actionFactory;
        $this->config = $config;
        $this->response = $response;
        $this->url = $url;
    }

    public function match(
        \Magento\Framework\App\RequestInterface $request
    ): ?\Magento\Framework\App\ActionInterface {

        $healthcheckKey = $this->findHealthcheckByRequest($request);

        if (empty($healthcheckKey)) {
            return null;
        }

        $request
            ->setModuleName('nfz')
            ->setControllerName('healthcheck')
            ->setActionName($healthcheckKey);

        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class,
            ['request' => $request]
        );
    }

    protected function findHealthcheckByRequest(\Magento\Framework\App\RequestInterface $request): ?string
    {
        $requestUrl = trim($request->getPathInfo(), '/');

        if (empty($this->config->getConfiguration())) {
            return null;
        }

        foreach ($this->config->getConfiguration()->getData() as $healthCheckKey => $config) {
            if (empty($config->getHealthcheckUrl())) {
                continue;
            }

            $configHealthcheckUrl = trim($config->getHealthcheckUrl(), '/');

            if ($requestUrl === $configHealthcheckUrl) {
                return $healthCheckKey;
            }
        }

        return null;
    }
}
