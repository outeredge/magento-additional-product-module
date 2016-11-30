<?php

namespace OuterEdge\AdditionalProduct\Ui\DataProvider\Product\Related;

/**
 * Class AdditionalDataProvider
 */
class AdditionalDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return 'additional';
    }
}