<?php
/**
 * Class NoOrdersAlert_Helper_Data
 *
 * @package   NoOrdersAlert
 * @author    Saad Yusuf
 */
class NoOrdersAlert_Helper_Data extends ModuleLogs_Helper_Abstract
{
    /**
     * const int
     *
     * Config path for the email address the alert will be sent to
     */
    const PATH_ENABLE = 'monitor_options/noordersalert/enable';

    /**
     * const string
     *
     * Config path for the monitoring start date
     */
    const PATH_START_DATE = 'monitor_options/noordersalert/start_date';

    /**
     * const string
     *
     * Config path for the email address the alert will be sent to
     */
    const PATH_EMAIL = 'monitor_options/noordersalert/email';

    /**
     * const string
     *
     * Path to the identity that will the alert email will come from
     */
    const PATH_IDENTITY = 'monitor_options/noordersalert/identity';

    /**
     * const string
     *
     * Path to the templateId that will be used
     */
    const PATH_ALERT_TEMPLATE = 'monitor_options/noordersalert/alert_template';

    /**
     * const string
     *
     * Path to the alert time in hours
     */
    const PATH_ALERT_TIME = 'monitor_options/noordersalert/alert_time';

    /**
     * const string
     *
     * Alert type, used to check if email has been already sent
     */
    const ALERT_TYPE = 'no_preorders';
}
