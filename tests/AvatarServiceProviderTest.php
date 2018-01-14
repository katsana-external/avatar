<?php

namespace Orchestra\Avatar\TestCase;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Orchestra\Avatar\AvatarServiceProvider;

class AvatarServiceProviderTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Avatar\AvatarServiceProvider is a deferred service
     * provider.
     *
     * @test
     */
    public function testIsDeferredService()
    {
        $stub = new AvatarServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /**
     * Test Orchestra\Avatar\AvatarServiceProvider::register()
     * method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Container\Container', '\Illuminate\Contracts\Foundation\Application[make]')->makePartial();
        $config = m::mock('\Illuminate\Contracts\Config\Repository', '\ArrayAccess');

        $app->shouldReceive('make')->twice()->with('config')->andReturn($config);
        $config->shouldReceive('get')->once()->with('orchestra.avatar')->andReturn([]);

        $stub = new AvatarServiceProvider($app);

        $this->assertNull($stub->register());
        $this->assertInstanceOf('\Orchestra\Avatar\AvatarManager', $app['orchestra.avatar']);
    }

    /**
     * Test Orchestra\Avatar\AvatarServiceProvider::boot()
     * method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = new Container();
        $config = m::mock('\Illuminate\Contracts\Config\Repository', '\ArrayAccess');

        $app->instance('config', $config);

        $stub = m::mock('\Orchestra\Avatar\AvatarServiceProvider[addConfigComponent,bootUsingLaravel]', [$app])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('addConfigComponent')->once()
                ->with('orchestra/avatar', 'orchestra/avatar', realpath(__DIR__.'/../resources/config'))
                ->andReturnNull()
            ->shouldReceive('bootUsingLaravel')->once()
                ->with(realpath(__DIR__.'/../resources'))
                ->andReturnNull();

        $this->assertNull($stub->boot());
    }

    /**
     * Test Orchestra\Avatar\AvatarServiceProvider::provides()
     * method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new AvatarServiceProvider(null);

        $this->assertContains('orchestra.avatar', $stub->provides());
    }
}
