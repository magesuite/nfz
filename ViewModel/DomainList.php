<?php

declare(strict_types=1);

namespace MageSuite\Nfz\ViewModel;

class DomainList implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public function __construct(
        protected \MageSuite\Nfz\Model\DomainVerifier $domainVerifier
    ) {}

    public function getDomainList(): array
    {
        return $this->domainVerifier->verify();
    }
}
