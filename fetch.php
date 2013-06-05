<?php
require 'vendor/autoload.php';
require 'config.php';

class FileValue
{
    protected $path;
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function get($default = null)
    {
        if(!file_exists($this->path))
            return $default;

        $content = file_get_contents($this->path);
        if($content === false)
            return $default;

        return $content;
    }

    public function set($value)
    {
        file_put_contents($this->path, $value);
    }
}

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

$lastFetchStore = new FileValue('last_fetch');
$feed = new SimplePie();

$feed->set_feed_url(FEED_URL);
$feed->enable_cache();
$feed->init();

$items = $feed->get_items();

$newItems = array();
// $lastFetch = time() - INTERVAL;
$lastFetch = (int)$lastFetchStore->get(0);
echo "Last fetch timestamp: $lastFetch\n";
$lastFetchStore->set(time());
foreach ($items as $item)
{
    if($item->get_date('U') > $lastFetch)
    {
        $newItems[] = $item;
        // echo $item->get_description(), "\n";
    }
    else // Items are already reverse-sorted by time
    {
        break;
    }
}

//var_dump($feed->get_item_quantity());

$count = count($newItems);
echo $count, " new items fetched\n";
if(!empty($newItems))
{
    echo "Sending notification\n";
    send_notification(format_items($newItems), $count);
    // echo format_items($newItems);
}
