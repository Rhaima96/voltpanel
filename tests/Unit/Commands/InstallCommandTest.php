<?php

namespace Rhaima\VoltPanel\Tests\Unit\Commands;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    #[Test]
    public function it_can_run_install_command(): void
    {
        $this->artisan('voltpanel:install', ['--no-deps' => true, '--force' => true, '--javascript' => true])
            ->expectsConfirmation('Would you like to publish CSS assets?', 'no')
            ->expectsConfirmation('Would you like to run the migrations now?', 'no')
            ->assertExitCode(0);
    }

    #[Test]
    public function it_registers_the_command(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('voltpanel:install', $commands);
    }
}
