<?php

declare(strict_types=1);

namespace App\Tests\Frontend\ContentModel;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\ContentModel\ParseStatus;

class ParsingStatusTest extends TestCase
{

    public function testStatusStrings()
    {
        $status = new ParseStatus('news');
        $status->parsing('id', 'number', 5);

        $expected = <<<EOL
Type: news
Field: id (number)
Content: 5

EOL;
        $this->assertEquals($expected, $status->__toString());

        $status->parsing(
            'team',
            'array',
            [
            [
                'name' => 'Bob Smith',
                'job_title' => 'Developer',
                'department' => [
                    'dept_name' => 'Design'
                ]
            ]
            ]
        );


        $expected = <<<EOL
Type: news
Field: team (array)
Content: array (
  0 => 
  array (
    'name' => 'Bob Smith',
    'job_title' => 'Developer',
    'department' => 
    array (
      'dept_name' => 'Design',
    ),
  ),
)

EOL;
        $this->assertEquals($expected, $status->__toString());

        $status2 = new ParseStatus('author', $status);
        $status2->parsing('name', 'text', 'Bob Smith');
        $status2->parsing('job_title', 'text', 'Developer');

        $expected = <<<EOL
Parents: news > team (array)
Type: author
Field: job_title (text)
Content: 'Developer'

EOL;
        $this->assertEquals($expected, $status2->__toString());

        $status2->parsing(
            'department',
            'relation',
            ['department' =>
            [
                'dept_name' => 'Design'
            ]
            ]
        );
        $status3 = new ParseStatus('department', $status2);
        $status3->parsing('dept_name', 'text', 'Design');

        $expected = <<<EOL
Parents: news > team (array) > author > department (relation)
Type: department
Field: dept_name (text)
Content: 'Design'

EOL;
        $this->assertEquals($expected, $status3->__toString());
    }
}
