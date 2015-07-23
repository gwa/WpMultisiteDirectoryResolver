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
use Gwa\Wordpress\MultisiteDirectoryResolver as MDR;

/**
 * MultisiteSubDomainResolverTest.
 *
 * @author  Daniel Bannert
 */
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

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $cwml->getHandler()->init();

        $filters = \Gwa\Wordpress\getAddedFilters();

        $this->assertEquals('network_admin_url', $filters[0]->filtername);
        $this->assertInternalType('array', $filters[0]->callback);

        $this->assertEquals('script_loader_src', $filters[1]->filtername);
        $this->assertInternalType('array', $filters[1]->callback);

        $this->assertEquals('style_loader_src', $filters[2]->filtername);
        $this->assertInternalType('array', $filters[2]->callback);
    }

    public function testFixNetworkLogin()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-login.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/foo/wp-admin/network';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($expectedadminurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultadminurl, '', ''));
    }

    public function testLeavesNetworkAdminUrlWhenCorrect()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $fixedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($fixedadminurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($fixedadminurl, '', ''));
    }

    public function testFixNetworkActive()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-activate.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-activate.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkSignup()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-signup.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-signup.php';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($expectedloginurl, $cwml->getHandler()->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixesAppURLWhenSiteUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = '/wp';
        $appFolder = 'app';

        $siteurl = 'http://example.org/projects/testWordpress/';
        $urlexpected = $siteurl.$appFolder;

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($siteurl.'/app', ''));
    }

    public function testLeavesStyleScriptURLWhenCorrectUrlPassed()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $siteurl = $domain.$installpath.'/';
        $urlpassed = $urlexpected = $siteurl.'/'.$installsubfolder.'/';

        $cwml = new MRM($installsubfolder, MRM::TYPE_SUBDOMAIN);
        $this->assertEquals($urlexpected, $cwml->getHandler()->fixStyleScriptPathFilter($urlpassed, ''));
    }

    public function testSetWpFolderName()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $cwml = new SubDomainStub($installsubfolder, MRM::TYPE_SUBDOMAIN);
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