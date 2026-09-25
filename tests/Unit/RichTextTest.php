<?php

namespace Tests\Unit;

use App\Support\RichText;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_controlled_rich_text_keeps_supported_content_and_removes_unsafe_markup(): void
    {
        $html = RichText::sanitize('<h2>Heading</h2><table><tr><td colspan="99">Value</td></tr></table><img src="/storage/media/example.jpg" alt="Desk"><script>alert(1)</script><a href="javascript:alert(1)">bad</a>');

        $this->assertStringContainsString('<h2>Heading</h2>', $html);
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('colspan="12"', $html);
        $this->assertStringContainsString('<img src="/storage/media/example.jpg" alt="Desk">', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_external_image_protocols_are_rejected(): void
    {
        $html = RichText::sanitize('<img src="javascript:alert(1)" alt="bad"><img src="data:image/svg+xml;base64,abc" alt="bad">');

        $this->assertSame('', $html);
    }
}
