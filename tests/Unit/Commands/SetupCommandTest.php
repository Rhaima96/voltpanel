<?php

namespace Rhaima\VoltPanel\Tests\Unit\Commands;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Rhaima\VoltPanel\Tests\TestCase;

class SetupCommandTest extends TestCase
{
    #[Test]
    public function it_registers_the_command(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('voltpanel:setup', $commands);
    }

    #[Test]
    public function it_has_correct_description(): void
    {
        $command = Artisan::all()['voltpanel:setup'];

        $this->assertStringContainsString('Setup', $command->getDescription());
    }
}
