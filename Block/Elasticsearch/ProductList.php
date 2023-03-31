<?php

declare(strict_types=1);

namespace MageSuite\Nfz\Block\Elasticsearch;

class ProductList extends \Magento\Catalog\Block\Product\ListProduct
{
    protected \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility;
    protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;
    protected \Smile\ElasticsuiteCore\Search\Request\Builder $requestBuilder;
    protected \Magento\Search\Model\SearchEngine $searchEngine;

    public function __construct(
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Smile\ElasticsuiteCore\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = [],
        ?\Magento\Catalog\Helper\Output $outputHelper = null
    ) {
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data,
            $outputHelper
        );
    }

    public function _construct()
    {
        parent::_construct();
        $this->setCollection($this->_getProductCollection());
    }

    protected function _getProductCollection(): \Magento\Catalog\Model\ResourceModel\Product\Collection
    {
        if ($this->_productCollection === null) {
            $docIds = $this->getEntityIdsForCollection();

            $pageSize = $this->_request->getParam('product_list_limit');
            $pageNumber = $this->_request->getParam('p', 1);

            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToFilter('entity_id', ['in' => $docIds]);

            if (is_numeric($pageSize)) {
                $collection->setPageSize($pageSize);
            } else {
                $collection->setCurPage(1);
            }
            $collection->setCurPage($pageNumber);

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    protected function _beforeToHtml(): self
    {
        $this->_collection = $this->_getProductCollection();

        $this->addToolbarBlock($this->_collection);

        return $this;
    }

    protected function addToolbarBlock(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection): void
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }

    protected function getToolbarFromLayout(): \MageSuite\Sorting\Block\Product\ProductList\Toolbar
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    protected function configureToolbar(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ): void {
        $orders = $this->getAvailableOrders();

        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $this->getSortBy();

        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $this->getDefaultDirection();

        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $modes = $this->getModes();

        if ($modes) {
            $toolbar->setModes($modes);
        }

        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
    }

    public function getIdentities(): array
    {
        return [];
    }

    protected function getAvailableOrders(): ?string
    {
        return $this->_request->getParam('product_list_order');
    }

    protected function getDefaultDirection(): ?string
    {
        return $this->_request->getParam('product_list_dir');
    }

    protected function getModes(): ?string
    {
        return $this->_request->getParam('product_list_mode');
    }

    protected function getEntityIdsForCollection(): array
    {
        $searchRequest = $this->requestBuilder->create(
            $this->_storeManager->getStore()->getId(),
            'catalog_view_container',
            1,
            25,
        );

        $queryResponse = $this->searchEngine->search($searchRequest);

        $docIds = array_map(
            function (\Magento\Framework\Api\Search\Document $doc) {
                return (int) $doc->getId();
            },
            $queryResponse->getIterator()->getArrayCopy()
        );

        return $docIds;
    }
}
