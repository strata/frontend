<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Twig\FrontendExtension;

class TwigTest extends TestCase
{

    public function testSlugify()
    {
        $twig = new FrontendExtension();

        $this->assertEquals('my-name-is-earl', $twig->slugify('My name is Earl'));
        $this->assertEquals('changing-spaces-here-and-here-and-123456-789', $twig->slugify('Changing spaces here "and here" and 123=456-789'));
        $this->assertEquals('a-longer-string-here-and-here', $twig->slugify('A longer string here ' . PHP_EOL . ' and here'));
        $this->assertEquals('my-title', $twig->slugify('<h1>My title</h1>'));
        $this->assertEquals('urljavascriptalertthe-secret-is-to-askwindowlocationreplacedo-something', $twig->slugify('url"javascript:alert(\'the secret is to ask.\');window.location.replace(\'Do something\')'));
        $this->assertEquals('a-lot-of-spaces', $twig->slugify('a lot    of         spaces'));
    }
}
