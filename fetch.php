<?php
require 'vendor/autoload.php';
require 'config.php';

\TreasureChest\Autoloader::register();

require 'GuidStore.php';
use \TreasureChest\Instance,
    \TreasureChest\Cache\Filesystem;


function format_items($items)
{
    $ret = '';
    foreach($items as $item)
    {
        $ret .= "<h2><a href='{$item->get_link()}'>{$item->get_title()}</a></h2>\n";
        $ret .= "<div><strong>{$item->get_date()}</strong></div>\n";
        $ret .= "<div>{$item->get_description()}</div>\n";
        $ret .= "<hr>\n";
    }
    return "<html><body>\n{$ret}\n</body></html>\n";
}

function send_notification($body, $count)
{
    $subject = SUBJECT_PREFIX . ' ' . $count . ' new item(s)';

    $mail = new PHPMailerLite();
    $mail->IsHTML(true);
    $mail->SetFrom(EMAIL_FROM);

    $mail->AddAddress(EMAIL_TO);
    $mail->Subject = $subject;

    $mail->MsgHTML($body);
    $mail->send();
}

$store = new \TreasureChest\Instance(new \TreasureChest\Cache\Filesystem(STORE_PATH));
$guidStore = new GuidStore($store);
$feed = new SimplePie();

$feed->set_feed_url(FEED_URL);
$feed->enable_cache();
$feed->init();

$items = $feed->get_items();

$newItems = array();
$newGuids = array();
// $lastFetch = time() - INTERVAL;

$lastFetch = (int)$store->fetch('last_fetch'); // will convert FALSE to 0
echo "Last fetch timestamp: $lastFetch\n";
$store->store('last_fetch', time());
foreach ($items as $item)
{
    if($item->get_date('U') > $lastFetch)
    {
        $id = $item->get_id(true); // use hash
        $newItems[$id] = $item;
        $newGuids[] = $id;
        // echo $item->get_description(), "\n";
    }
    else // Items are already reverse-sorted by time
    {
        break;
    }
}

$count = count($newItems);
echo $count, " new items fetched\n";

// Remove previously detected GUIDs
$newGuids = $guidStore->diff($newGuids);
$count = count($newGuids);
echo $count, " items left after filtering out\n";

// Keep only items which were not detected yet
$newItems = array_intersect_key($newItems, array_flip($newGuids));

$guidStore->add_guids($newGuids);

if(!empty($newItems))
{
    echo "Sending notification\n";
    send_notification(format_items($newItems), $count);
    // echo format_items($newItems);
}
