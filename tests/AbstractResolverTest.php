<?php
namespace Gwa\Wordpress\Test;

use Gwa\Wordpress\AbstractResolver;
use Gwa\Wordpress\WpBridge\MockeryWpBridge;

class AbstractResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testFixNetworkLogin()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-login.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-login.php';

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkAdminUrl()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultadminurl = $domain.$installpath.'/foo/wp-admin/network';
        $expectedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $this->assertEquals($expectedadminurl, $cwml->fixNetworkAdminUrlFilter($defaultadminurl, '', ''));
    }

    public function testLeavesNetworkAdminUrlWhenCorrect()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $fixedadminurl = $domain.$installpath.'/'.$installsubfolder.'/wp-admin/network';

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $this->assertEquals($fixedadminurl, $cwml->fixNetworkAdminUrlFilter($fixedadminurl, '', ''));
    }

    public function testFixNetworkActive()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-activate.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-activate.php';

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixNetworkSignup()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $defaultloginurl = $domain.$installpath.'/foo/wp-signup.php';
        $expectedloginurl = $domain.$installpath.'/'.$installsubfolder.'/wp-signup.php';

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $this->assertEquals($expectedloginurl, $cwml->fixNetworkAdminUrlFilter($defaultloginurl, '', ''));
    }

    public function testFixWpProtocolFilter()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urls = [
            'url' => 'https://example.org/path/to/my/project/app/plugins/',
            'url' => 'http://example.org/path/to/my/project/app/uploaded/',
        ];

        $expectedUrls = [
            'url' => 'http://example.org/path/to/my/project/app/plugins/',
            'url' => 'http://example.org/path/to/my/project/app/uploaded/',
        ];

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('isSsl')
            ->andReturn('http://');

        $this->assertEquals($expectedUrls, $cwml->fixWpProtocolFilter($urls));
    }

    public function testfixWpDoubleSlashFilter()
    {
        $domain = 'http://example.org';
        $installpath = '/path/to/my/project';
        $installsubfolder = 'foo/wp';

        $urls = [
            'https://example.org/path/to/my/project//app/plugins/',
            'http://example.org/path/to/my/project//app/uploaded/',
        ];

        $expectedUrls = [
            'https://example.org/path/to/my/project/app/plugins/',
            'http://example.org/path/to/my/project/app/uploaded/',
        ];

        $cwml = new Resolver($installsubfolder);
        $cwml->setWpBridge(new MockeryWpBridge());
        $cwml->getWpBridge()->mock()
            ->shouldReceive('isSsl')
            ->andReturn('http://');

        $this->assertEquals($expectedUrls, $cwml->fixWpDoubleSlashFilter($urls));
    }
}

/**
 * Test Class.
 */
class Resolver extends AbstractResolver
{
}
