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
use Gwa\Wordpress\MockeryWpBridge\MockeryWpBridge;

/**
 * MultisiteResolverManager.
 *
 * @author  Daniel Bannert
 */
class MultisiteResolverManager extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Gwa\Wordpress\MockeryWpBridge\WpBridge', $mrm->getWpBridge());

        $mrm = new MRM('/wp/', MRM::TYPE_FOLDER, new MockeryWpBridge());
        $this->assertInstanceOf('\Gwa\Wordpress\MockeryWpBridge\MockeryWpBridge', $mrm->getWpBridge());
    }
}
