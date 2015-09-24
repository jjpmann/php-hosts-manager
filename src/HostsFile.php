<?php

namespace HostsManager;

class HostsFile
{
    /**
     * File Resource.
     *
     * @var resource
     **/
    protected $file; // resource

    /**
     * Full file path to the file resouce.
     *
     * @var string
     **/
    protected $filepath;

    /**
     * Constructor.
     *
     * @param string|null $filepath The path of the hosts file, normally /etc/hosts as set in default
     */
    public function __construct($filepath = '/etc/hosts')
    {
        $this->init($filepath);
    }

    /**
     * boots up class by using filepath to create file object.
     *
     * @param string|null $filepath The path of the hosts file, normally /etc/hosts as set in default
     **/
    protected function init($filepath)
    {
        try {
            $this->file = new \SplFileObject($filepath);
        } catch (Exception $e) {
            throw new \LogicException('This is not a file', 1);
        }

        if (!$this->file->isFile()) {
            throw new \Exception('This is not a file', 1);
        }

        if (!$this->file->isReadable()) {
            throw new \Exception('This file is not readable', 1);
        }

        if (!$this->file->isWritable()) {
            //throw new \Exception("This file is not writable", 1);
        }

        $this->filepath = $this->file->getPathName();
    }

    /**
     * grab protected/private attributes.
     *
     * @param string $name of a class attribute
     * 
     * @return mixed
     **/
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }

    /**
     * check hosts file if $host is present.
     *
     * @param string The $host is what we are searching for in the hosts file
     *
     * @return bool
     **/
    public function check($host)
    {
        $this->validHost($host);

        $host = trim($host);
        $valid = false; // init as false
        $this->file->rewind();

        while (!$this->file->eof()) {
            $pattern = '/\b'.$host.'\b/i';
            if (preg_match($pattern, $this->file->current())) {
                $valid = trim($this->file->current());
                break; // Once you find the string, you should break out the loop.
            }
            $this->file->next();
        }

        return $valid;
    }

    /**
     * add new host to hosts file.
     *
     * @param string The $host to be added to the hosts file
     * @param string The $ip   to be added along with host to hosts file
     *
     * @throws RuntimeException if already exists or cant write to file
     * 
     * @return bool
     **/
    public function add($host, $ip)
    {
        $this->validHost($host);
        $this->validIp($ip);

        if ($this->check($host)) {
            throw new \RuntimeException('Host already exists in the file');
        }

        $this->back();

        $record = "\n$ip $host";
        $file = new \SplFileObject($this->filepath, 'a');
        $ok = $file->fwrite($record);

        if (!$ok) {
            throw new \RuntimeException('Could not write to file');
        }

        return true;
    }

    /**
     * update host in hosts file.
     *
     * @param string The $host to be added to the hosts file
     * @param string The $ip   to be added along with host to hosts file
     *
     * @throws RuntimeException if already exists or cant write to file
     * 
     * @return bool
     **/
    protected function backup()
    {
    }

    /**
     * update host in hosts file.
     *
     * @param string The $host to be added to the hosts file
     * @param string The $ip   to be added along with host to hosts file
     *
     * @throws RuntimeException if already exists or cant write to file
     * 
     * @return bool
     **/
    public function update($host, $ip)
    {
        $this->validHost($host);
        $this->validIp($ip);

        return $this->remove($host) && $this->add($host, $ip);
    }

    /**
     * remove host from hosts file.
     *
     * @param string The $host to be removed to the hosts file
     *
     * @throws RuntimeException if host does no exists or cant write to file
     * 
     * @return bool
     **/
    public function remove($host)
    {
        $this->validHost($host);

        if (!$this->check($host)) {
            throw new \RuntimeException('Host does not exists in the file');
        }

        $this->back();

        $file = new \SplFileObject($this->filepath, 'w+');

        $write = false;
        while (!$file->eof()) {
            $pattern = '/\b'.$host.'\b/i';
            if (preg_match($pattern, $file->current())) {
                $write = $file->fwrite('');
                break;
            }
            $file->next();
        }

        if ($write) {
            throw new \RuntimeException('Could not write to file');
        }

        return true;
    }

    /**
     * validates host and/or ip.
     *
     * @throws RuntimeException is domain is not valid
     **/
    public function validate($host = null, $ip = null)
    {
        $this->validHost($host);
        $this->validIp($ip);
    }

    /**
     * validates host name.
     *
     * @throws RuntimeException is domain is not valid
     **/
    protected function validHost($host)
    {
        if ($host === null) {
            return;
        }
        if (false === $this->isValidDomainName($host)) {
            throw new \RuntimeException('Domain invalid');
        }
    }

    /**
     * validates ip.
     *
     * @throws RuntimeException is IP is not valid
     **/
    protected function validIp($ip)
    {
        if ($ip === null) {
            return;
        }
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    /**
     * validates callback is callable.
     *
     * @throws RuntimeException is callback cant be called
     **/
    protected function validCallback($callback)
    {
        if ($callback == null) {
            return;
        }
        if (!is_callable($callback)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    /**
     * helper function to validate domain name.
     *
     * @return boot
     **/
    protected function isValidDomainName($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match('/^.{1,253}$/', $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); //length of each label
    }
}
