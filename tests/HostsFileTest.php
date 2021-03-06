<?php

namespace HostsManager;

class HostsFileTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        copy(__DIR__.'/stub_file.txt', __DIR__.'/file.txt');
    }

    public function tearDown()
    {
        unlink(__DIR__.'/file.txt');
        @unlink(__DIR__.'/file.txt.bkup.1');
        @unlink(__DIR__.'/file.txt.bkup.2');
        @unlink(__DIR__.'/file.txt.bkup.3');
    }

    public function testClassCreate()
    {
        $hp = new HostsFile();
        $this->assertInstanceOf('HostsManager\HostsFile', $hp);
    }

    public function testFileExists()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertInstanceOf('HostsManager\HostsFile', $hp);
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
        $this->assertEquals($hp->check('test.app'), '127.0.0.4 test.app');
        $this->assertFalse($hp->check('test.cm'));
        $this->assertEquals($hp->check('test.com'), '127.0.0.3 test.com');
        $this->assertFalse($hp->check('test.appp'));
    }

    public function testAdd()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $this->assertTrue($hp->add('google.com', '127.0.0.3'));
        $this->assertEquals($hp->check('google.com'), '127.0.0.3 google.com');
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
        $this->assertEquals($hp->check('test.com'), '127.0.0.3 test.com');
    }

    public function testExceptionRemove()
    {
        $this->setExpectedException('RuntimeException');
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->remove('not.here.app');
    }

    public function testBackup()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $hp->backup();
        $this->assertTrue(file_exists(__DIR__.'/file.txt.bkup.1'));
    }

    public function testRollback()
    {
        $hp = new HostsFile(__DIR__.'/file.txt');
        $file1 = file_get_contents(__DIR__.'/file.txt');

        $hp->add('google.com', '127.0.0.2');
        $file2 = file_get_contents(__DIR__.'/file.txt');
        $this->assertNotEquals($file1, $file2);

        $hp->rollback();
        $file3 = file_get_contents(__DIR__.'/file.txt');
        $this->assertEquals($file1, $file3);
    }
}
