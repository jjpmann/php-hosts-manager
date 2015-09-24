<?php

namespace HostsManager;

use RuntimeException;

class SudoProcessException extends RuntimeException
{
    public function test()
    {
        echo "<pre>"; var_dump( 
            get_class_methods($this),
            trim($this->getMessage())
            //$this->getMessage()
            //$this->__toString()
        ); exit;
        
    }
}
