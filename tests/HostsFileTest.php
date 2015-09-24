<?php

namespace HostsManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

use Mockery as m;

class HostsFileTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        copy(__DIR__.'/stub_file.txt', __DIR__.'/file.txt');
    }

    public function tearDown()
    {
    }

    public function testClassCreate()
    {
        $hp = new HostsFile();
        $this->assertInstanceOf(HostsFile::class, $hp);
    }

    public function testFileExists()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertInstanceOf(HostsFile::class, $hp);
    }

    public function testExceptionFileDoesNotExists()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'.file');
    }

    public function testExceptionIsNotDirectory()
    {
        $this->setExpectedException('LogicException');
        $hp = new HostsFile(__DIR__);
    }

    public function testExceptionValidHosts()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->check('test.@a');
    }

    public function testValidIps()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->add('test.com', '127.xx');
    }

    public function testCheck()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertFalse($hp->check('test.a'));
        $this->assertTrue($hp->check('test.app'));
        $this->assertFalse($hp->check('test.cm'));
        $this->assertTrue($hp->check('test.com'));
        $this->assertFalse($hp->check('test.appp'));
    }

    public function testAdd()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertTrue($hp->add('google.com', '127.0.0.3'));
        $this->assertTrue($hp->check('google.com'));
    }

    public function testExceptionAdd()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->add('google.com', '127.0.0.2');
        $hp->add('google.com', '127.0.0.2');
    }

    public function testRemove()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertTrue($hp->remove('test.app'));
    }

    public function testExceptionRemove()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->remove('not.here.app');
    }
}
