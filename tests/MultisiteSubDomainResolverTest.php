<?php
namespace Gwa\Wordpress\Test;

use Gwa\Wordpress\MultisiteDirectoryResolver as MDR;
use Gwa\Wordpress\MultisiteResolverManager as MRM;
use Gwa\Wordpress\WpBridge\MockeryWpBridge;

class MultisiteSubDomainResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testCheckForDefinitionException()
    {
        new MRM('', MRM::TYPE_SUBDOMAIN);
    }

    public function testInitFilter()
    {
        $installsubfolder = 'foo/wp';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN, new MockeryWpBridge());
        $cwml->getHandler()->init();

        $filters = $cwml->getWpBridge()->getAddedFilters();

        $this->assertEquals(5, count($filters));

        $this->assertEquals('network_admin_url', $filters[0]->filtername);
        $this->assertInternalType('array', $filters[0]->callback);

        $this->assertEquals('script_loader_src', $filters[1]->filtername);
        $this->assertInternalType('array', $filters[1]->callback);

        $this->assertEquals('style_loader_src', $filters[2]->filtername);
        $this->assertInternalType('array', $filters[2]->callback);

        $this->assertEquals('upload_dir', $filters[3]->filtername);
        $this->assertInternalType('array', $filters[3]->callback);

        $this->assertEquals('upload_dir', $filters[4]->filtername);
        $this->assertInternalType('array', $filters[4]->callback);
    }

    public function testFixesAppURLWhenSiteUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = '/wp';
        $appFolder = 'app';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlexpected = $siteurl.$appFolder;

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn($domain)
            ->shouldReceive('escUrl')
            ->andReturnUsing(function ($str) {return $str;});

        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($siteurl.'/app', ''));
    }

    public function testLeavesStyleScriptURLWhenCorrectUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = $domain.$installpath.'/';
        $urlpassed = $urlexpected = $siteurl.'/'.$installsubfolder.'/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('escUrl')
            ->andReturnUsing(function ($str) {return $str;});

        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($urlpassed, ''));
    }

    public function testSetWpFolderName()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $cwml = new SubDomainStub($installsubfolder, MRM::TYPE_SUBDOMAIN, new MockeryWpBridge());
        $this->assertEquals('wp', $cwml->getWordpressName());
        $this->assertNotEquals('web', $cwml->getWordpressName());
    }
}

class SubDomainStub extends MDR
{
    public function getWordpressName()
    {
        return $this->wpFolderName;
    }
}
