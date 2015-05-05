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
 * @version     0.0.4
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
        $installsubfolder = 'foo/wp';

        $cwml = new MDR($installsubfolder);
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

    public function testFixNetworkLogin()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-login.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/foo/wp-admin/network';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedadminurl, $cwml->fixNetworkAdminUrlFilter($defaultadminurl, '', ''));
    }

    public function testFixNetworkActive()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-activate.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-activate.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkSignup()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-signup.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-signup.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixSiteAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/wp-admin';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedadminurl, $cwml->fixSiteUrlFilter($defaultadminurl, '', ''));
    }

    public function testFixSiteLoginUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/wp-login.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($expectedloginurl, $cwml->fixSiteUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixSiteUrlFilterWhenWpAdminPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlAdmin = $domain.$installpath.'/wp-admin/';
        $correctUrlAdmin = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlAdmin, $cwml->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($urlAdmin, $cwml->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlAdmin, $cwml->fixSiteUrlFilter($correctUrlAdmin, '', ''));
    }

    public function testFixSiteUrlFilterWhenWpLoginPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlLogin = $domain.$installpath.'/wp-login.php';
        $correctUrlLogin = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlLogin, $cwml->fixSiteUrlFilter($urlLogin, '', ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrlLogin, $cwml->fixSiteUrlFilter($correctUrlLogin, '', ''));
    }

    public function testFixesStyleScriptURLWhenExternUrlPassed($value = '')
    {
        $installsubfolder = 'foo/wp';

        $externalUrl = '//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&subset=latin%2Clatin-ext&ver=4.2.1';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($externalUrl, $cwml->fixStyleScriptPathFilter($externalUrl, ''));
    }

    public function testFixesStyleScriptURLWhenPluginUrlPassed($value = '')
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlPlugin = $domain.$installpath.'/app/plugins/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlPlugin, $cwml->fixStyleScriptPathFilter($urlPlugin, ''));

        $urlPlugin = 'http://example.org/projects/testWordpress/web/wp/app/plugins/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlPlugin, $cwml->fixStyleScriptPathFilter($urlPlugin, ''));
    }

    public function testFixesStyleScriptURLWhenSiteUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlexpected = $siteurl.'/'.$installsubfolder;

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlexpected, $cwml->fixStyleScriptPathFilter($siteurl, ''));
    }

    public function testLeavesStyleScriptURLWhenCorrectUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = $domain.$installpath.'/';
        $urlpassed = $urlexpected = $siteurl.'/'.$installsubfolder.'/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($urlexpected, $cwml->fixStyleScriptPathFilter($urlpassed, ''));
    }

    public function testFixWpIncludeFolder()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $url = $domain.$installpath.'/wp-includes/';
        $correctUrl = $domain.$installpath.'/'.$installsubfolder.'/wp-includes/';

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixWpIncludeFolder($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertNotEquals($url, $cwml->fixWpIncludeFolder($url, ''));

        $cwml = new MDR($installsubfolder);
        $this->assertEquals($correctUrl, $cwml->fixWpIncludeFolder($correctUrl, ''));
    }

    public function testSetWpFolderName()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $cwml = new CwmlStub($installsubfolder);
        $this->assertEquals('wp', $cwml->getWordpressName());
        $this->assertNotEquals('web', $cwml->getWordpressName());
    }
}

class CwmlStub extends MDR
{
    public function getWordpressName()
    {
        return $this->wpFolderName;
    }
}
