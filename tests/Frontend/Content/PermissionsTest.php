<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Api\ApiPermissionHelper;

class PermissionsTest extends TestCase
{

    public function testDefault()
    {
        $perms = new ApiPermissionHelper();
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testRead()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::READ);
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testWrite()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::WRITE);
        $this->assertFalse($perms->read());
        $this->assertTrue($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testDelete()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::DELETE);
        $this->assertFalse($perms->read());
        $this->assertFalse($perms->write());
        $this->assertTrue($perms->delete());
    }

    public function testReadWrite()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::WRITE | ApiPermissionHelper::READ);
        $this->assertTrue($perms->read());
        $this->assertTrue($perms->write());
        $this->assertFalse($perms->delete());
    }

    public function testReadDelete()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::DELETE | ApiPermissionHelper::READ);
        $this->assertTrue($perms->read());
        $this->assertFalse($perms->write());
        $this->assertTrue($perms->delete());
    }

    public function testReadWriteDelete()
    {
        $perms = new ApiPermissionHelper(ApiPermissionHelper::WRITE | ApiPermissionHelper::READ | ApiPermissionHelper::DELETE);
        $this->assertTrue($perms->read());
        $this->assertTrue($perms->write());
        $this->assertTrue($perms->delete());
    }
}
