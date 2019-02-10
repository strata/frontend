<?php
declare(strict_types=1);

namespace App\Tests\Search;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Page;

class TrimContentTest extends TestCase
{

    public function testTrimContent()
    {
        $page = new Page();

        $this->assertEquals('The quick', $page->trimContent('The quick brown fox jumps over the lazy dog', 10));
        $this->assertEquals('The quick brown fox', $page->trimContent('The quick brown fox jumps over the lazy dog', 24));
        $this->assertEquals('The quick brown fox jumps', $page->trimContent('The quick brown fox jumps over the lazy dog', 29));
        $this->assertNotEquals(29, strlen($page->trimContent('The quick brown fox jumps over the lazy dog', 29)));

        $this->assertEquals('The quick brown fox jumps over', $page->trimContent('The quick brown fox jumps over the lazy dog', 30));
        $this->assertEquals('The quick brown fox jumps over', $page->trimContent('The quick brown fox jumps over the lazy dog', 31));

        $content = <<<EOD
Lorem ipsum dolor amet coloring book vice shoreditch, occupy skateboard hot chicken chillwave microdosing wolf tacos leggings.

Gastropub freegan try-hard, artisan master cleanse flannel activated charcoal crucifix snackwave you probably haven't heard of them.
EOD;
        $expected1 = <<<EOD
Lorem ipsum dolor amet coloring book vice shoreditch, occupy skateboard hot chicken chillwave microdosing wolf tacos leggings.

Gastropub
EOD;
        $expected2 = <<<EOD
Lorem ipsum dolor amet coloring book vice shoreditch, occupy skateboard hot chicken chillwave microdosing wolf tacos leggings.

Gastropub freegan try-
EOD;

        $this->assertEquals($expected1, $page->trimContent($content, 142));
        $this->assertEquals($expected2, $page->trimContent($content, 150));

        $this->assertEquals('The quick brown fox', $page->trimContent('The <strong>quick</strong> brown fox jumps over the lazy dog', 24));

        /**
         * @license http://creativecommons.org/licenses/by-sa/2.5/ Test content by Mozilla Contributors is licensed under CC-BY-SA 2.5
         */
        $content = <<<EOD
              <!-- article content -->
              <article id="wikiArticle">
                
                    <p><span class="seoSpan"><strong>CSS3</strong> is the latest evolution of the <em>Cascading Style Sheets</em> language and aims at extending CSS2.1. It brings a lot of long-awaited novelties, like rounded corners, shadows, <a href="/en-US/docs/Web/Guide/CSS/Using_CSS_gradients" title="Using CSS gradients">gradients</a>, <a href="/en-US/docs/Web/Guide/CSS/Using_CSS_transitions" title="CSS transitions">transitions</a> or <a href="/en-US/docs/Web/Guide/CSS/Using_CSS_animations" title="CSS animations">animations</a>, as well as new layouts like <a href="/en-US/docs/Web/Guide/CSS/Using_multi-column_layouts" title="Using CSS multi-column layouts">multi-columns</a>, <a href="/en-US/docs/Web/Guide/CSS/Flexible_boxes">flexible box</a> or grid layouts.</span> Experimental parts are vendor-prefixed and should either be avoided in production environments, or used with extreme caution as both their syntax and semantics can change in the future.</p>

<h2 id="Modules_and_the_standardization_process">Modules and the standardization process</h2>

<p>CSS Level 2 needed 9 years, from August 2002 to June 2011 to reach the Recommendation status. This was due to the fact that a few secondary features held back the whole specification. In order to accelerate the standardization of non-problematic features, the <a rel="noopener" href="http://www.w3.org/blog/CSS/" class="external" title="http://www.w3.org/blog/CSS/">CSS Working Group</a> of the W3C, in a decision referred as the <a rel="noopener" href="http://fantasai.inkedblade.net/weblog/2011/inside-csswg/modules" class="external" title="http://fantasai.inkedblade.net/weblog/2011/inside-csswg/modules">Beijing doctrine</a>, divided CSS in smaller components called <em>modules</em> . Each of these modules is now an independent part of the language and moves towards standardization at its own pace. While some modules are already W3C Recommendations, other still are early Working Drafts. New modules are also added when new needs are identified.</p>
EOD;

        $expected1 = <<<EOD
CSS3 is the latest evolution of the Cascading Style Sheets
EOD;
        $expected2 = <<<EOD
CSS3 is the latest evolution of the Cascading Style Sheets language and aims at extending CSS2.1. It brings a lot of long-awaited novelties, like rounded corners, shadows, gradients, transitions or animations, as well as new layouts like multi-columns, flexible box or grid layouts. Experimental parts are vendor-prefixed and should either be avoided in production environments, or used with extreme caution as both their syntax and semantics can change in the future.

Modules and the
EOD;

        $this->assertEquals($expected1, $page->trimContent($content, 60));
        $this->assertEquals($expected2, $page->trimContent($content, 500));

        $content = <<<EOD
<table class="fullwidth-table">
 <tbody>
  <tr>
   <td style="width: 30%; background-color: rgb(128,255,128);"><strong><a rel="noopener" lang="en" hreflang="en" href="https://drafts.csswg.org/css-color-3/" title="The 'CSS Color Module Level 3' specification" class="external">CSS Color Module Level 3</a></strong></td>
   <td><span class="spec-REC">Recommendation</span> since June 7th, 2011</td>
  </tr>
  <tr>
   <td colspan="2">
    <p>Adds the <a href="/en-US/docs/Web/CSS/opacity" title="The opacity CSS property sets the transparency of an element or the degree to which content behind an element is visible."><code>opacity</code></a> property, and the <code>hsl</code><code>()</code>, <code>hsla()</code>, <code>rgba()</code> and <code>rgb()</code> functions to create <a href="/en-US/docs/Web/CSS/color_value" title="The &lt;color> CSS data type represents a color in the sRGB color space. A &lt;color> may also include an alpha-channel transparency value, indicating how the color should composite with its background."><code>&lt;color&gt;</code></a> values. It also defines the <code>currentColor</code> keyword as a valid color.</p>

    <p>The <code>transparent</code> color is now a real color (thanks to the support for the alpha channel) and is a now an alias for <code>rgba(0,0,0,0.0)</code> .</p>

    <p>It deprecates the <a rel="noopener" href="http://www.w3.org/TR/CSS2/ui.html#system-colors" class="external">system-color keywords that shouldn't be used in a production environment anymore</a>.</p>
   </td>
  </tr>
 </tbody>
</table>
EOD;

        $expected = <<<EOD
CSS Color Module
EOD;

        $this->assertEquals($expected, $page->trimContent($content, 20));
    }
}
