<?php

namespace OuterEdge\AdditionalProduct\Model\Product;

class Link extends \Magento\Catalog\Model\Product\Link
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