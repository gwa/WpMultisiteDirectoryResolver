<?php
namespace Gwa\Wordpress\Test;

use Gwa\Wordpress\MultisiteResolverManager as MRM;
use Gwa\Wordpress\WpBridge\MockeryWpBridge;

class MultisiteManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testCheckForDefinitionException()
    {
        new MRM('', MRM::TYPE_SUBDOMAIN);
    }

    public function testGetHandler()
    {
        $mrm = new MRM('/wp/', MRM::TYPE_SUBDOMAIN);
        $this->assertInstanceOf('\Gwa\Wordpress\MultisiteSubDomainResolver', $mrm->getHandler());

        $mrm = new MRM('/wp/', MRM::TYPE_FOLDER);
        $this->assertInstanceOf('\Gwa\Wordpress\MultisiteDirectoryResolver', $mrm->getHandler());
    }

    public function testMockeryWpBridgeInstance()
    {
        $mrm = new MRM('/wp/', MRM::TYPE_SUBDOMAIN);
        $this->assertInstanceOf('\Gwa\Wordpress\WpBridge\WpBridge', $mrm->getWpBridge());

        $mrm = new MRM('/wp/', MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertInstanceOf('\Gwa\Wordpress\WpBridge\MockeryWpBridge', $mrm->getWpBridge());
    }
}
