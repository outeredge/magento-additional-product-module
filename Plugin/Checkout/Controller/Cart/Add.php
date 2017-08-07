<?php

namespace OuterEdge\AdditionalProduct\Plugin\Checkout\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as CartAdd;

class Add
{
    public function beforeExecute(CartAdd $subject)
    {
        $related    = $subject->getRequest()->getParam('related_product');
        $additional = $subject->getRequest()->getParam('additional_product');

        if (!empty($additional)) {
            if (!empty($related)) {
                $additional = $related . ',' . $additional;
            }
            $subject->getRequest()->setParams(['related_product' => $additional]);
        }

        return null;
    }
}
