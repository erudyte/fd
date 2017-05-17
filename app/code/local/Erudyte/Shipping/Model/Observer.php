<?php

class Erudyte_Shipping_Model_Observer
{
    /**
     *

     * @param $observer
     * @return $this
     * @author Graham Crocker <graham.paul.crocker@gmail.com>
     */
    public function displayFreeDeliveryNotice(Varien_Event_Observer $observer)
    {
        $shippingConfig = $this->getShippingConfig();
        if (!$shippingConfig) {
            return;
        }
        if (!$shippingConfig['enabled']) {
            return;
        }

        $checkoutSession = $this->getCheckoutSession();

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $checkoutSession->getQuote();
        if (!$quote || $quote->getItemsCount() === 0) {
            return;
        }
        $freeDeliveryMinimum = $shippingConfig['free_delivery_minimum'];
        //get the quote subtotal
        $subtotal = $quote->getSubtotal();
        if ($message = $this->getFreeDeliveryMessage($freeDeliveryMinimum, $subtotal)) {
            $checkoutSession->addNotice($message);
        }
    }


    /**
     * @param float $freeDeliveryMinimum
     * @param float $basketTotal
     * @return string
     * @author Graham Crocker <graham.paul.crocker@gmail.com>
     */
    protected function getFreeDeliveryMessage($freeDeliveryMinimum, $basketTotal)
    {
        $difference = $freeDeliveryMinimum - $basketTotal;
        if (!((int) $difference > 0)) {
            $display = "Spend another £$difference to qualify for Free Delivery";
        } else {
            $display = '';
        }
        return $display;
    }

    /**
     * Potential Mocking area
     *  @author Graham Crocker <graham.paul.crocker@gmail.com>
     */
    protected function getShippingConfig()
    {
        return Mage::getStoreConfig('shipping_rules/free_delivery');
    }

    /**
     * Potential Mocking area
     * @return Mage_Checkout_Model_Session
     * @author Graham Crocker <graham.paul.crocker@gmail.com>
     */
    protected function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}