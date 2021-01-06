<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Api\Permissions;

class PermissionsTest extends TestCase
{

    public function testDefault()
    {
        $perms = new Permissions();
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testRead()
    {
        $perms = new Permissions(Permissions::READ);
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testWrite()
    {
        $perms = new Permissions(Permissions::WRITE);
        $this->assertFalse($perms->read());
        $this->assertTrue($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testDelete()
    {
        $perms = new Permissions(Permissions::DELETE);
        $this->assertFalse($perms->read());
        $this->assertFalse($perms->write());
        $this->assertTrue($perms->delete());
    }

    public function testReadWrite()
    {
        $perms = new Permissions(Permissions::WRITE | Permissions::READ);
        $this->assertTrue($perms->read());
        $this->assertTrue($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testReadDelete()
    {
        $perms = new Permissions(Permissions::DELETE | Permissions::READ);
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertTrue($perms->delete());
    }

    public function testReadWriteDelete()
    {
        $perms = new Permissions(Permissions::WRITE | Permissions::READ | Permissions::DELETE);
        $this->assertTrue($perms->read());
        $this->assertTrue($perms->write());
        $this->assertTrue($perms->delete());
    }
}
