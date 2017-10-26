<?php
/**
 * Class NoShipmentsAlert_Helper_Data
 *
 * @package   NoShipmentsAlert
 * @author    Saad Yusuf
 */
class NoShipmentsAlert_Helper_Data extends ModuleLogs_Helper_Abstract
{
    /**
     * const int
     *
     * Config path for enable alerts option
     */
    const PATH_ENABLE = 'monitor_options/noshipmentsalert/enable';

    /**
     * const string
     *
     * Config path for the monitoring start date
     */
    const PATH_START_DATE = 'monitor_options/noshipmentsalert/start_date';

    /**
     * const string
     *
     * Config path for the email address the alert will be sent to
     */
    const PATH_EMAIL = 'monitor_options/noshipmentsalert/email';

    /**
     * const string
     *
     * Path to the identity that will the alert email will come from
     */
    const PATH_IDENTITY = 'monitor_options/noshipmentsalert/identity';

    /**
     * const string
     *
     * Path to the templateId that will be used
     */
    const PATH_ALERT_TEMPLATE = 'monitor_options/noshipmentsalert/alert_template';

    /**
     * const string
     *
     * Path to the alert time in hours
     */
    const PATH_ALERT_TIME = 'monitor_options/noshipmentsalert/alert_time';

    /**
     * const string
     *
     * Alert type, used to check if email has been already sent
     */
    const ALERT_TYPE = 'no_shipments';
}
