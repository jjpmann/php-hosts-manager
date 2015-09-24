<?php

namespace HostsManager;

class HostsFileException extends \RuntimeException
{

    protected $raw = false;

    public function message()
    {

        return 'message';
        

        return parent::getMessage();
    }

}
