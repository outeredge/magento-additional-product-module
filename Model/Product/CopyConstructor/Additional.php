<?php

namespace OuterEdge\AdditionalProduct\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product\CopyConstructorInterface;
use Magento\Catalog\Model\Product;

class Additional implements CopyConstructorInterface
{
    /**
     * Build product links
     *
     * @param Product $product
     * @param Product $duplicate
     * @return void
     */
    public function build(Product $product, Product $duplicate)
    {
        $data = [];
        $attributes = [];

        $link = $product->getLinkInstance();
        $link->useAdditionalLinks();
        foreach ($link->getAttributes() as $attribute) {
            if (isset($attribute['code'])) {
                $attributes[] = $attribute['code'];
            }
        }
        
        /** @var \Magento\Catalog\Model\Product\Link $link  */
        foreach ($product->getAdditionallLinkCollection() as $link) {
            $data[$link->getLinkedProductId()] = $link->toArray($attributes);
        }
        $duplicate->setAdditionalLinkData($data);
    }
}
