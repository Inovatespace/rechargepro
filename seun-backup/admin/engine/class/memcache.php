<?php
class Cache extends engine
{

    private $prefix, $cur_key, $cur_cache;
    static private $MEMCACHE_SERVERS = array(
        "127.0.0.1:11211", //web1
        );

    public static function getInstance()
    {
        if (self::config('enable_memcache'))
        {
            static $instance = null;
            if ($instance == null)
                $instance = new Cache();
            return $instance;
        }
    }

    public function __construct()
    {

        if (self::config('enable_memcache'))
        {
            if (!function_exists('memcache_connect'))
            {
                die('Memcache is not currently installed...');
            } else
            {

                $this->memcache = new Memcache;
                foreach (self::$MEMCACHE_SERVERS as $server)
                {
                    $this->memcache->addServer($server);
                }

                $this->prefix = "aiw_";

            }
        }
    }

    public function exists($key)
    {
        if (self::config('enable_memcache'))
        {
            if ($this->memcache->get($this->prefix . $key))
            {
                $this->cur_cache = $this->memcache->get($this->prefix . $key);
                $this->cur_key = $this->prefix . $key;
                return true;
            } else
            {
                return false;
            }
        } else
            {
                return false;
            }
    }
    
        public function version()
    {
        if (self::config('enable_memcache'))
        {
           
          return $this->memcache->getVersion();
        }
    }

    public function delete($key)
    {
        if (self::config('enable_memcache') && self::exists($key))
        {
            if ($this->memcache->get($this->prefix . $key))
            {
                return $this->memcache->delete($this->prefix . $key);

            } else
            {
                return false;
            }
        }
    }

    public function flush()
    {
        if (self::config('enable_memcache'))
        {
            $this->memcache->flush();
        }
    }

    public function update($key, $data, $interval)
    {
        if (self::config('enable_memcache') && self::exists($key))
        {
            $interval = (isset($interval)) ? $interval : 60 * 60 * 0.15;

            if ($this->prefix . $this->cur_key)
            {
                if (!empty($this->cur_cache))
                {
                    return $this->memcache->replace($this->cur_key, $data, MEMCACHE_COMPRESSED, $interval);
                }
            } elseif ($this->memcache->get($this->prefix . $key))
            {
                return $this->memcache->replace($this->prefix . $key, $data, MEMCACHE_COMPRESSED,
                    $interval);
            } else
            {
                return false;
            }
        }
    }

    public function get($key)
    {
        if (self::config('enable_memcache'))
        {
            if (($this->prefix . $key) == $this->cur_key)
            {
                return $this->cur_cache;
            } else
            {
                return $this->memcache->get($this->prefix . $key);
            }
        }
    }

    public function set($key, $data, $interval)
    {
        if (self::config('enable_memcache'))
        {
            $interval = (isset($interval)) ? $interval : 900;
            return $this->memcache->set($this->prefix . $key, $data, MEMCACHE_COMPRESSED, $interval);
        }
    }

}

?>