<?php
/**
 * Class NoShipmentsAlert_Model_Observer
 *
 * Check if no shipments have been sent within config specified time
 * If no shipments sent then add to monitor alert collection
 *
 * @package   NoShipmentsAlert
 * @author    Saad Yusuf
 */
class NoShipmentsAlert_Model_Observer
{
    /**
     * helper object used to get config data
     *
     * @var Monitor_Helper_Data
     */
    protected $_helper = null;

    /**
     * store id, will be added to alert item
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Adds alert to collection if conditions are met
     *
     * @param Varien_Event_Observer $observer observer
     *
     * @return Varien_Event_Observer
     */
    public function check(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();

        if (!$collection instanceof Monitor_Model_Alert_Collection) {
            return $this;
        }

        $this->_helper = Mage::helper('monitor');

        // get all the stores
        $stores = Mage::app()->getStores();

        // loop through all the stores
        foreach ($stores as $store) {
            $this->_storeId = $store->getStoreId();
            $email          = $this->_helper->getConfig(
                NoShipmentsAlert_Helper_Data::PATH_EMAIL, $this->_storeId
            );
            $enable         = $this->_helper->getConfig(
                NoShipmentsAlert_Helper_Data::PATH_ENABLE, $this->_storeId
            );

            // check if monitoring is enabled
            if (!$enable) {
                return $this;
            }

            // check if email address is set
            if ($email) {
                // get the last order time and compare with current time if hours match send email
                if ($this->_checkSendEmail()) {
                    $item = $this->_createAlertItem();
                    $collection->addItem($item);
                }
            } else {
                Mage::helper('noshipmentsalert')->log('No alert email address specified.');
            }
        }

        return $this;
    }

    /**
     * Function _checkSendEmail
     *
     * Check if an email alert should be added to the alerts
     * Check if any shipments have been placed in specified time.
     *
     * @return bool
     */
    protected function _checkSendEmail()
    {
        // get the last shipment time
        $shipment = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('created_at')
            ->addAttributeToFilter('store_id', $this->_storeId)
            ->setOrder('created_at', 'DESC')
            ->setPageSize(1)
            ->setCurPage(1);

        if ($shipment->count() != 0) {
            $lastShipment = $shipment->getFirstItem();
            $shipmentTime = $lastShipment->getCreatedAt();

            if ($shipmentTime) {
                return $this->_helper->checkAddAlert(
                    $shipmentTime,
                    $this->_helper->getConfig(
                        NoShipmentsAlert_Helper_Data::PATH_ALERT_TIME,
                        $this->_storeId
                    ),
                    NoShipmentsAlert_Helper_Data::ALERT_TYPE,
                    $this->_storeId
                );
            }
        } else {
            // no records exist check monitor start date in config
            // to determine if an alert should be sent
            $startDate = $this->_helper->getConfig(
                NoShipmentsAlert_Helper_Data::PATH_START_DATE,
                $this->_storeId
            );
            if ($startDate) {
                return $this->_helper->checkAddAlert(
                    $startDate,
                    $this->_helper->getConfig(
                        NoShipmentsAlert_Helper_Data::PATH_ALERT_TIME,
                        $this->_storeId
                    ),
                    NoShipmentsAlert_Helper_Data::ALERT_TYPE,
                    $this->_storeId
                );
            }
        }

        return false;
    }

    /**
     * Create a alert item that will be added to the collection
     *
     * @return Varien_Object
     */
    protected function _createAlertItem()
    {
        $alert = $this->_helper->createAlert(
            array(
                 'email' => NoShipmentsAlert_Helper_Data::PATH_EMAIL,
                 'identity' => NoShipmentsAlert_Helper_Data::PATH_IDENTITY,
                 'alert_template' => NoShipmentsAlert_Helper_Data::PATH_ALERT_TEMPLATE,
                 'alert_time' => NoShipmentsAlert_Helper_Data::PATH_ALERT_TIME,
                 'alert_type' => NoShipmentsAlert_Helper_Data::ALERT_TYPE,
                 'store_id' => $this->_storeId
            )
        );

        return $alert;
    }
}
