<?php

use \TreasureChest\Instance,
    \TreasureChest\Cache\Filesystem;

\TreasureChest\Autoloader::register();
class GuidStore
{
    protected $store;
    protected $guids;
    protected $limit = 300;
    protected $key = 'guids';

    public function __construct($store)
    {
        $this->store = $store;
        $this->guids = $this->store->fetch($this->key);

        if($this->guids === FALSE)
        {
            $this->guids = array();
        }
    }

    public function is_set($guid)
    {
        return in_array($guid, $this->guids);
    }

    /**
     * Filter-out and return GUIDs not present in the store
     */
    public function diff($guids)
    {
        return array_diff($this->guids, $guids);
    }

    public function add_guids($guids)
    {
        // Prepend new guids
        $this->guids = array_merge($guids, $this->guids);
        // Keep the array limited to $limit values
        array_slice($this->guids, 0, $this->limit);

        $this->flush();
    }

    public function add_guid($guid)
    {
        array_unshift($this->guids, $guid);
        // Keep the array limited to $limit values
        array_slice($this->guids, 0, $this->limit);

        $this->flush();
    }

    protected function flush()
    {
        $this->store->store($this->key, $this->guids);
    }
}
