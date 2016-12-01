<?php

namespace OuterEdge\AdditionalProduct\Block\Product\ProductList;

class Additional extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Additional item collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    protected $_itemCollection;

    /**
     * Prepare Additional items data
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Additional
     */
    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */

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
     * @return \Magento\Catalog\Block\Product\ProductList\Additional
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Additional items collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }
}
