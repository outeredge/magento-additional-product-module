<?php

namespace OuterEdge\AdditionalProduct\Model\Product;

use Magento\Catalog\Model\Product\Link as ProductLink;

class Link extends ProductLink
{
    const LINK_TYPE_ADDITIONAL = 6;

     /**
     * @return $this
     */
    public function useAdditionalLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_ADDITIONAL);
        return $this;
    }
}
