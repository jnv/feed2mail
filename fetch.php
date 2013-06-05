<?php
require 'vendor/autoload.php';
require 'config.php';


function send_notification($items)
{
    $to = EMAIL_TO;
    $subject = SUBJECT_PREFIX . ' ' . count($items) . ' new item(s)';
    $headers = 'From: ';
}


$feed = new SimplePie();

$feed->set_feed_url(FEED_URL);
$feed->enable_cache();
$feed->init();

$items = $feed->get_items();

$newItems = array();
$lastFetch = time() - INTERVAL;
foreach ($items as $item)
{
    if($item->get_date('U') > $lastFetch)
    {
        $newItems[] = $item;
        echo $item->get_description(), "\n";
    }
    else // Items are already reverse-sorted by time
    {
        break;
    }
}

var_dump($feed->get_item_quantity());

$count = count($newItems);
echo $count, " new items fetched\n";
if(!empty($newItems))
{
    echo "Sending notification\n";
    send_notification($newItems);
}
