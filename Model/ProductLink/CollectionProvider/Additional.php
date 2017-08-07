<?php

namespace OuterEdge\AdditionalProduct\Model\ProductLink\CollectionProvider;

use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;
use Magento\Catalog\Model\Product;

class Additional implements CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        return $product->getAdditionalProducts();
    }
}
