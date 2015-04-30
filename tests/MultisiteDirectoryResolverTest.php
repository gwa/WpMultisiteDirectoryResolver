<?php

namespace Gwa\Wordpress\Test;

/**
 * Composer Multisite fixer.
 *
 * @author      Daniel Bannert <bannert@greatwhiteark.com>
 * @copyright   2015 Great White Ark
 *
 * @link        http://www.greatwhiteark.com
 *
 * @license     MIT
 *
 * @version     0.0.3-dev
 */

use Gwa\Wordpress\MultisiteDirectoryResolver as MDR;

/**
 * MultisiteDirectoryResolverTest.
 *
 * @author  Daniel Bannert
 *
 * @since   0.0.2-dev
 */
class MultisiteDirectoryResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testCheckForDefinitionException()
    {
        return new MDR();
    }

    public function testInitFilter()
    {
        $cwml = new MDR('web/wp');
        $cwml->init();

        $filters = \Gwa\Wordpress\getAddedFilters();

        $this->assertEquals(5, count($filters));

        $this->assertEquals('network_admin_url', $filters[0]->filtername);
        $this->assertInternalType('array', $filters[0]->callback);

        $this->assertEquals('site_url', $filters[1]->filtername);
        $this->assertInternalType('array', $filters[1]->callback);

        $this->assertEquals('script_loader_src', $filters[2]->filtername);
        $this->assertInternalType('array', $filters[2]->callback);

        $this->assertEquals('style_loader_src', $filters[3]->filtername);
        $this->assertInternalType('array', $filters[3]->callback);

        $this->assertEquals('includes_url', $filters[4]->filtername);
        $this->assertInternalType('array', $filters[4]->callback);
    }

    public function testSetNetworkUrl()
    {
        $installsubfolder = 'web/wp';

        $cwml = new CwmlStub($installsubfolder, 'test');
        $this->assertEquals('test', $cwml->getNetworkUrl());

        $cwml2 = new CwmlStub($installsubfolder);
        $this->assertEquals('wp/wp-admin/network/', $cwml2->getNetworkUrl());
    }

    public function testFixNetworkAdminUrlFilter()
    {
        $installsubfolder = 'web/wp';

        $url = 'http://example.org/projects/testWordpress/web/wp-admin/';
        $correctUrl = 'http://example.org/projects/testWordpress/web/wp/wp-admin/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixNetworkAdminUrlFilter($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($url, $cwml->fixNetworkAdminUrlFilter($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixNetworkAdminUrlFilter($correctUrl, ''));
    }

    public function testFixSiteUrlFilterWhenWpAdminPassed()
    {
        $installsubfolder = 'web/wp';

        $urlAdmin = 'http://example.org/projects/testWordpress/wp-admin/';
        $correctUrlAdmin = 'http://example.org/projects/testWordpress/web/wp/wp-admin/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlAdmin, $cwml->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($urlAdmin, $cwml->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlAdmin, $cwml->fixSiteUrlFilter($correctUrlAdmin, '', ''));
    }

    public function testFixSiteUrlFilterWhenWpLoginPassed()
    {
        $installsubfolder = 'web/wp';

        $urlLogin = 'http://example.org/projects/testWordpress/wp-login.php';
        $correctUrlLogin = 'http://example.org/projects/testWordpress/web/wp/wp-login.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlLogin, $cwml->fixSiteUrlFilter($urlLogin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($urlLogin, $cwml->fixSiteUrlFilter($urlLogin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlLogin, $cwml->fixSiteUrlFilter($correctUrlLogin, '', ''));
    }

    public function testFixesStyleScriptURLWhenExternUrlPassed($value='')
    {
        $installsubfolder = 'web/wp';

        $googleUrl = '//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&subset=latin%2Clatin-ext&ver=4.2.1';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($googleUrl, $cwml->fixStyleScriptPathFilter($googleUrl, ''));
    }

    public function testFixesStyleScriptURLWhenPluginUrlPassed($value='')
    {
        $installsubfolder = 'web/wp';

        $urlPlugin = 'http://example.org/projects/testWordpress/app/plugins/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlPlugin, $cwml->fixStyleScriptPathFilter($urlPlugin, ''));

        $urlPlugin = 'http://example.org/projects/testWordpress/web/wp/app/plugins/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlPlugin, $cwml->fixStyleScriptPathFilter($urlPlugin, ''));
    }

    public function testFixesStyleScriptURLWhenSiteUrlPassed()
    {
        $installsubfolder = 'web/wp';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlexpected = $siteurl.'/'.$installsubfolder;

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlexpected, $cwml->fixStyleScriptPathFilter($siteurl, ''));
    }

    public function testLeavesStyleScriptURLWhenCorrectUrlPassed()
    {
        $installsubfolder = 'web/wp';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlpassed = $urlexpected = $siteurl.'/'.$installsubfolder.'/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlexpected, $cwml->fixStyleScriptPathFilter($urlpassed, ''));
    }

    public function testFixWpIncludeFolder()
    {
        $installsubfolder = 'web/wp';

        $url = 'http://example.org/projects/testWordpress/wp-includes/';
        $correctUrl = 'http://example.org/projects/testWordpress/web/wp/wp-includes/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixWpIncludeFolder($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($url, $cwml->fixWpIncludeFolder($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixWpIncludeFolder($correctUrl, ''));
    }

    public function testSetWpFolderName()
    {
        $installsubfolder = 'web/wp';

        $cwml = new CwmlStub($installsubfolder);
        $this->assertEquals('wp', $cwml->getWordpressName());
        $this->assertNotEquals('web', $cwml->getWordpressName());
    }
}

class CwmlStub extends MDR
{
    public function getNetworkUrl()
    {
        return $this->networkUrl;
    }

    public function getWordpressName()
    {
        return $this->wpFolderName;
    }
}
