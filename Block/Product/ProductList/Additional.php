<?php

namespace OuterEdge\AdditionalProduct\Block\Product\ProductList;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection as ProductLinkCollection;

class Additional extends AbstractProduct
{
    /**
     * @var ProductLinkCollection
     */
    protected $_itemCollection;

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('product');

        $this->_itemCollection = $product->getAdditionalProductCollection()->addAttributeToSelect(
            $this->_catalogConfig->getProductAttributes()
        )->setPositionOrder()->addStoreFilter();

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Before rendering html process
     * Prepare items collection
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Additional items collection
     *
     * @return ProductLinkCollection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }
}
