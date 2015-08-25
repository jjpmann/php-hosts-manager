<?php

namespace HostsManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

use Mockery as m;

class HostsProcessTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testRunAddConfirmAttributes()
    {
        $host   = 'test.app';
        $ip     = '10.10.10.10';

        $hp1 = HostsProcess::add($host, $ip);

        $this->assertEquals($hp1->host, $host);
        $this->assertEquals($hp1->ip, $ip);

        $callback = m::mock('foo');

        $hp2 = HostsProcess::add($host, $ip, $callback);

        // $this->assertEquals($hp2->host, $host);
        // $this->assertEquals($hp2->ip, $ip);

    }
}
