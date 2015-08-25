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

    public function testClassCreate()
    {
        $hp = new HostsProcess();
        $this->assertInstanceOf(HostsProcess::class, $hp);
    }

    public function testClassCreateWithCallback()
    {
        $func = function() { return true; };

        $hp = new HostsProcess($func);
        
        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertInstanceOf('Closure', $hp->callback);
        $this->assertSame($hp->callback, $func);

        $func2 = function() { return false; };
        
        $hp->callback($func2);
        $this->assertInstanceOf('Closure', $hp->callback);
        $this->assertSame($hp->callback, $func2);
    }

    public function testRunAddAttributes()
    {
        $host   = 'test.app';
        $ip     = '10.10.10.10';

        $hp = new HostsProcess();

        $hp = $hp->add($host, $ip);

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->host, $host);
        $this->assertEquals($hp->ip, $ip);
    }

    public function testRunUpdateAttributes()
    {
        $host   = 'test.app';
        $ip     = '10.10.10.10';

        $hp = new HostsProcess();

        $hp = $hp->update($host, $ip);

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->host, $host);
        $this->assertEquals($hp->ip, $ip);
    }

    public function testRunRemoveAttributes()
    {
        $host   = 'test.app';
        $ip     = null;
        $message= "You have removed {$host} from your Hosts file.\n";

        $hp = new HostsProcess();

        $hp = $hp->remove($host);

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->host, $host);
        $this->assertEquals($hp->ip, $ip);
        $this->assertEquals($hp->message, $message);
    }

    public function testRunCheckAttributes()
    {
        $host   = 'test.app';
        $ip     = null;
        

        $hp = new HostsProcess();

        $hp = $hp->check($host);

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->host, $host);
        $this->assertEquals($hp->ip, $ip);
    }

    public function testRunRollbackAttributes()
    {
        $message   = "You have rolled back your Hosts file.\n";

        $hp = new HostsProcess();

        $hp = $hp->rollback();

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->message, $message);
    }

    public function testSetOutput()
    {
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface');

        $hp = new HostsProcess();

        $hp = $hp->output($output);

        $this->assertInstanceOf(HostsProcess::class, $hp);
        $this->assertEquals($hp->output, $output);
    }

    public function _testRun()
    {
        //$hp = new HostsProcess();
        $hp = m::mock('HostsManager\HostsProcess[runScript]');

        $hp->shouldReceive('runScript')->andReturn($hp);

        $this->assertEquals($hp, $hp->run());
    }
}
