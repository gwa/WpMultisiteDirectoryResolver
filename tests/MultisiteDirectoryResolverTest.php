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
 */

use Gwa\Wordpress\MultisiteResolverManager as MRM;
use Gwa\Wordpress\MultisiteSubDomainResolver as MSDR;
use Gwa\Wordpress\MockeryWpBridge\MockeryWpBridge;

/**
 * MultisiteDirectoryResolverTest.
 *
 * @author  Daniel Bannert
 */
class MultisiteDirectoryResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testCheckForDefinitionException()
    {
        new MRM('', MRM::TYPE_FOLDER);
    }

    public function testInitFilter()
    {
        $installsubfolder = 'foo/wp';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getHandler()->init();

        $filters = $cwml->getWpBridge()->getAddedFilters();

        $this->assertEquals(5, count($filters));

        $this->assertEquals('network_admin_url', $filters[0]->filtername);
        $this->assertInternalType('array', $filters[0]->callback);

        $this->assertEquals('script_loader_src', $filters[1]->filtername);
        $this->assertInternalType('array', $filters[1]->callback);

        $this->assertEquals('style_loader_src', $filters[2]->filtername);
        $this->assertInternalType('array', $filters[2]->callback);

        $this->assertEquals('site_url', $filters[3]->filtername);
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

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/foo/wp-admin/network';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedadminurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultadminurl, '', ''));
    }

    public function testLeavesNetworkAdminUrlWhenCorrect()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $fixedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($fixedadminurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($fixedadminurl, '', ''));
    }

    public function testFixNetworkActive()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-activate.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-activate.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkSignup()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-signup.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-signup.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixSiteAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/wp-admin';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedadminurl, $cwml->getHandler()->fixSiteUrlFilter($defaultadminurl, '', ''));
    }

    public function testFixSiteLoginUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/wp-login.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixSiteUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixSiteUrlFilterWhenWpAdminPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlAdmin = $domain.$installpath.'/wp-admin/';
        $correctUrlAdmin = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrlAdmin, $cwml->getHandler()->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertNotEquals($urlAdmin, $cwml->getHandler()->fixSiteUrlFilter($urlAdmin, '', ''));

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrlAdmin, $cwml->getHandler()->fixSiteUrlFilter($correctUrlAdmin, '', ''));
    }

    public function testFixSiteUrlFilterWhenWpLoginPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlLogin = $domain.$installpath.'/wp-login.php';
        $correctUrlLogin = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrlLogin, $cwml->getHandler()->fixSiteUrlFilter($urlLogin, '', ''));

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrlLogin, $cwml->getHandler()->fixSiteUrlFilter($correctUrlLogin, '', ''));
    }

    public function testFixesStyleScriptURLWhenExternUrlPassed($value = '')
    {
        $installsubfolder = 'foo/wp';

        $externalUrl = '//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&subset=latin%2Clatin-ext&ver=4.2.1';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn('http://www.example.com')
            ->shouldReceive('escUrl')
            ->andReturnUsing(function($str){return $str;});

        $this->assertEquals($externalUrl, $cwml->getHandler()->fixStyleScriptPathFilter($externalUrl, ''));
    }

    public function testFixesStyleScriptURLWhenPluginUrlPassed($value = '')
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urlPlugin = $domain.$installpath.'//app/plugins/';
        $expectedUrl = $domain.$installpath.'/app/plugins/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn($domain)
            ->shouldReceive('escUrl')
            ->andReturnUsing(function($str){return $str;});

        $this->assertEquals($expectedUrl, $cwml->getHandler()->fixStyleScriptPathFilter($urlPlugin, ''));

        $urlPlugin = 'http://example.org/projects/testWordpress/web/wp//app/plugins/';
        $expectedUrl = 'http://example.org/projects/testWordpress/web/wp/app/plugins/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn($domain)
            ->shouldReceive('escUrl')
            ->andReturnUsing(function($str){return $str;});

        $this->assertEquals($expectedUrl, $cwml->getHandler()->fixStyleScriptPathFilter($urlPlugin, ''));
    }

    public function testFixesStyleScriptURLWhenSiteUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlexpected = $siteurl.$installsubfolder;

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn($siteurl)
            ->shouldReceive('escUrl')
            ->andReturnUsing(function($str){return $str;});

        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($siteurl, ''));
    }

    public function testLeavesStyleScriptURLWhenCorrectUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = $domain.$installpath.'/';
        $urlpassed = $urlexpected = $siteurl.'/'.$installsubfolder.'/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('siteUrl')
            ->andReturn($domain)
            ->shouldReceive('escUrl')
            ->andReturnUsing(function($str){return $str;});

        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($urlpassed, ''));
    }

    public function testFixWpIncludeFolder()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $url = $domain.$installpath.'/wp-includes/';
        $correctUrl = $domain.$installpath.'/'.$installsubfolder.'/wp-includes/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrl, $cwml->getHandler()->fixWpIncludeFolder($url, ''));

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertNotEquals($url, $cwml->getHandler()->fixWpIncludeFolder($url, ''));

        $cwml = new MRM($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals($correctUrl, $cwml->getHandler()->fixWpIncludeFolder($correctUrl, ''));
    }

    public function testSetWpFolderName()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $cwml = new CwmlStub($installsubfolder, MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertEquals('wp', $cwml->getWordpressName());
        $this->assertNotEquals('web', $cwml->getWordpressName());
    }
}

class CwmlStub extends MSDR
{
    public function getWordpressName()
    {
        return $this->wpFolderName;
    }
}