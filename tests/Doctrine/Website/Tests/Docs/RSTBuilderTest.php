<?php

namespace Doctrine\Website\Tests\Docs;

use Gregwar\RST\HTML\Kernel as HTMLKernel;
use Doctrine\Website\Docs\RSTBuilder;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\RST\Kernel;
use Gregwar\RST\Builder;
use PHPUnit\Framework\TestCase;

class RSTBuilderTest extends TestCase
{
    /** @var string */
    private $sculpinSourcePath;

    /** @var Builder */
    private $builder;

    /** @var string */
    private $projectsPath;

    /** @var RSTBuilder */
    private $rstBuilder;

    protected function setUp()
    {
        $this->sculpinSourcePath = __DIR__.'/resources/sculpin-source';
        $this->projectsPath = __DIR__.'/resources';
        $this->builder = new Builder(new Kernel(new HTMLKernel(), []));

        $this->rstBuilder = new RSTBuilder(
            $this->sculpinSourcePath,
            $this->builder,
            $this->projectsPath
        );
    }

    public function testGetDocuments()
    {
        $this->assertEquals([], $this->rstBuilder->getDocuments());
    }

    public function testProjectHasDocs()
    {
        $project = new Project([
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $this->assertTrue($this->rstBuilder->projectHasDocs($project));
    }

    public function testBuildRSTDocs()
    {
        $project = new Project([
            'docsSlug' => 'example-project',
            'docsRepositoryName' => 'example-project',
            'docsPath' => '/docs',
        ]);

        $version = new ProjectVersion([
            'slug' => '1.0',
        ]);

        $this->rstBuilder->buildRSTDocs($project, $version);

        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/index.html');
        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/about.html');
        $this->assertSculpinSourceFileExists('/projects/example-project/en/1.0/example.html');

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<a class="section-anchor" id="index" name="index"></a><h1 class="section-header"><a href="#index">Index<i class="fas fa-link"></i></a></h1>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="about.html">About1</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="example.html">Example</a></li>'
        );

        $expected = <<<HTML
<ul><li class="dash">List item 1
multiline</li>
<li class="dash">List item 2</li>
<li class="dash">List item 3
multiline</li>
</ul>
HTML;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );

        $expected = <<<HTML
<ul><li class="dash">
    Alternate list item 1
    multiline</li>
<li class="dash">
    Alternate list item 2</li>
<li class="dash">
    Alternate list item 3
    multiline</li>
</ul>
HTML;

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            $expected
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<a id="lists"></a>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<p><a href="#lists">go to lists</a></p>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<p><a href="#anchor-section">@Anchor Section</a></p>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="about.html">About2</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<ul><li class="dash"> <a href="#test_reference_anchor">@Test Reference Anchor</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_anchor">Cross Ref</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_1_anchor">Cross Ref Section 1</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_2_anchor">Cross Ref Section 2</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="cross-ref.html#cross_ref_section_a_anchor">Cross Ref Section A</a></li>'
        );

        $this->assertSculpinSourceFileContains(
            '/projects/example-project/en/1.0/index.html',
            '<li class="dash"><a href="https://www.doctrine-project.org">TestLink</a></li>'
        );
    }

    private function assertSculpinSourceFileExists(string $path)
    {
        $this->assertFileExists($this->sculpinSourcePath.$path);
    }

    private function assertSculpinSourceFileContains(string $path, string $contains)
    {
        $html = file_get_contents($this->sculpinSourcePath.$path);

        $this->assertContains($contains, $html);
    }
}
