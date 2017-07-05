<?php
/**
 * Sends all notifications to a specified email.
 *
 * Consider using this class together with Debug_ErrorHook_RemoveDupsWrapper
 * to avoid mail server flooding when a lot of errors arrives.
 */

require_once __DIR__ . "/Util.php";
require_once __DIR__ . "/TextNotifier.php";

class Debug_ErrorHook_RssNotifier extends Debug_ErrorHook_TextNotifier
{

    public function __construct($whatToSend)
    {
        parent::__construct($whatToSend);
    }

    protected function _notifyText($subject, $body)
    {
        $rss = new AirRssNotifier();
        $rss->addItem($subject, $body);
    }
}
