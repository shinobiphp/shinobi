<?php
declare(strict_types=1);
namespace Shinobi\Tests;
use PHPUnit\Framework\TestCase;
use Shinobi\Runtime\Runtime;
final class RuntimeTest extends TestCase
{
    public function testResolvesShinobiAppThroughCodejitsu(): void
    {
        $app = (new Runtime(dirname(__DIR__)))->resolve('app://shinobi');
        self::assertSame('app://shinobi', $app->uri);
        self::assertSame('spec://shinobi/app#1.0.0', $app->spec());
        self::assertContains('capability://runtime#1.0.0', $app->data['capabilities']);
    }
}
