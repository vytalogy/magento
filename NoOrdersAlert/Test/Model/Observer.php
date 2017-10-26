<?php
/**
 * NoOrdersAlert
 *
 * Check if alert email gets added to the alert collection
 * 
 * @package   NoOrdersAlert
 * @author    Saad Yusuf
 */
class NoOrdersAlert_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{
    /**
     * const string
     *
     * email address used to verify email are being sent
     */
    const TEST_EMAIL = 'hello@example.com';

    /**
     * model object used for the test
     *
     * @var NoOrdersAlert_Model_Observer
     */
    protected $_model = null;

    /**
     * model object used for the test
     *
     * @var Monitor_Model_Alert
     */
    protected $_monitor = null;

    /**
     * Setup function, initialise the model object
     *
     * @return null
     */
    protected function setUp()
    {
        $this->_model   = Mage::getModel('noordersalert/observer');
        $this->_monitor = Mage::getModel('monitor/alert');

        Mage::getConfig()->setNode(
            'global/models/core/rewrite/email_template', 'Test_Model_Email_Template'
        );
        // This is a hack to get the runtime config changes to take effect
        Mage::getModel('core/email_template');
        $mailTemplate = Mage::getModel('core/email_template');
        $mailTemplate->resetStoredEmails();
    }

    /**
     * function testNoAlertNew
     *
     * Test the order alert does not get added to the collection
     *
     * @return null
     *
     * @test
     * @loadFixture
     */
    public function testNoAlertNew()
    {
        // check the model is the correct instance
        $this->assertInstanceOf(
            'NoOrdersAlert_Model_Observer', $this->_model,
            'Model is an instance of NoOrdersAlert_Model_Observer'
        );

        // update the fixture created_at field
        // so the order time is more recent than the alert time
        $order        = Mage::getModel('sales/order')->load(10);
        $newOrderTime = Mage::app()->getLocale()->storeTimeStamp($order->getStoreId());
        $newOrderTime = date("Y-m-d H:m:s", $newOrderTime);
        $order->setCreatedAt($newOrderTime);
        $order->save();

        // call the monitor alert collection and clear it
        $collection = $this->_monitor->getCollection()->clear();

        // call the send function
        $this->_monitor->send();

        // see if the event was dispatched
        $this->assertEventDispatched(
            'monitor_send_before', 'monitor_send_before event was dispatched'
        );

        // there should be one item in the collection
        $this->assertEquals(
            0, count($collection), 'the collection is still empty'
        );

        $mailTemplate = Mage::getModel('core/email_template');
        $emails_sent  = $mailTemplate->getSentEmails();

        // 0 email should be sent
        $this->assertEquals(
            0, count($emails_sent),
            'Check no email have been sent - order is new'
        );
    }

    /**
     * function testSendAlertOld
     *
     * Test the order alert gets added to the collection as its older than the alert time
     *
     * @return null
     *
     * @test
     * @loadFixture
     */
    public function testSendAlertOld()
    {
        // check the model is the correct instance
        $this->assertInstanceOf(
            'NoOrdersAlert_Model_Observer', $this->_model,
            'Model is an instance of NoOrdersAlert_Model_Observer'
        );

        // call the monitor alert collection and clear it
        $collection = $this->_monitor->getCollection()->clear();

        // call the send function
        $this->_monitor->send();

        // see if the event was dispatched
        $this->assertEventDispatched(
            'monitor_send_before', 'monitor_send_before event was dispatched'
        );

        // there should be one item in the collection
        $this->assertEquals(
            1, count($collection), '1 item exists in the collection'
        );

        $mailTemplate = Mage::getModel('core/email_template');
        $emails_sent  = $mailTemplate->getSentEmails();

        // 1 email should be sent
        $this->assertEquals(
            1, count($emails_sent),
            'Check alert email have been sent - order is old'
        );

        // 2 check if the email is sent to the right address
        $emails = $emails_sent[0];

        $class    = new ReflectionClass($emails);
        $property = $class->getProperty("_to");
        $property->setAccessible(true);
        $emailTo = $property->getValue($emails);

        $this->assertEquals(
            self::TEST_EMAIL, $emailTo[0],
            'email to address is correct'
        );
    }

    /**
     * function testSendAlert
     *
     * Test the order alert gets added to the collection as it match's the alert time
     *
     * @return null
     *
     * @test
     * @loadFixture
     */
    public function testSendAlert()
    {
        // check the model is the correct instance
        $this->assertInstanceOf(
            'NoOrdersAlert_Model_Observer', $this->_model,
            'Model is an instance of NoOrdersAlert_Model_Observer'
        );

        // get the alert time
        $alertTime = Mage::getStoreConfig(NoOrdersAlert_Helper_Data::PATH_ALERT_TIME);
        $this->assertGreaterThan(
            0, $alertTime, 'Alert time is greater than zero.'
        );

        // update the fixture created_at field
        // so it matches the current time minus alert time
        $order        = Mage::getModel('sales/order')->load(10);
        $newOrderTime = Mage::app()->getLocale()->storeTimeStamp($order->getStoreId());
        if ($alertTime == 1) {
            $newOrderTime = strtotime('-1 hour', $newOrderTime);
        } else {
            $newOrderTime = strtotime('-'.$alertTime.' hours', $newOrderTime);

        }
        $newOrderTime = date("Y-m-d H:i:s", $newOrderTime);
        $order->setCreatedAt($newOrderTime);

        // call the monitor alert collection and clear it
        $collection = $this->_monitor->getCollection()->clear();

        // call the send function
        $this->_monitor->send();

        // see if the event was dispatched
        $this->assertEventDispatched(
            'monitor_send_before', 'monitor_send_before event was dispatched'
        );

        // there should be one item in the collection
        $this->assertEquals(
            1, count($collection), '1 item exists in the collection'
        );

        $mailTemplate = Mage::getModel('core/email_template');
        $emails_sent  = $mailTemplate->getSentEmails();

        // 0 email should be sent
        $this->assertEquals(
            1, count($emails_sent),
            'Check alert email have been sent - order time = alert time'
        );

        // 2 check if the email is sent to the right address
        $emails = $emails_sent[0];

        $class    = new ReflectionClass($emails);
        $property = $class->getProperty("_to");
        $property->setAccessible(true);
        $emailTo = $property->getValue($emails);

        $this->assertEquals(
            self::TEST_EMAIL, $emailTo[0],
            'email to address is correct'
        );
    }

}
