<?php
/**
 * @package Tests
 * @subpackage Unit
 */

/**
 * Test class for Nethgui_Core_HostConfiguration.
 * Generated by PHPUnit on 2011-03-18 at 10:30:21.
 * @package Tests
 * @subpackage Unit
 */
class Nethgui_Core_HostConfigurationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Nethgui_Core_HostConfiguration
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new Nethgui_Core_HostConfiguration($this->getMock('Nethgui_Core_UserInterface'));
        $this->object->setPolicyDecisionPoint(new Nethgui_Authorization_PermissivePolicyDecisionPoint());
    }

    public function testPdp()
    {
        $db = $this->object->getDatabase('testdb');
        $this->assertSame($this->object->getPolicyDecisionPoint(), $db->getPolicyDecisionPoint());
    }

    /**
     * Asserts a database object interface has the same PDP of the fixture.
     */
    public function testGetDatabase()
    {
        $db = $this->object->getDatabase('testdb');
        $this->assertInstanceOf('Nethgui_Core_ConfigurationDatabase', $db, $message);
    }

    /**
     *
     */
    public function testSignalEvent1()
    {
        $exitStatusInfo = $this->object->signalEvent("not-exist-event");
        $this->assertNotEquals(0, $exitStatusInfo->getExitStatus());
    }

    public function testSignalEvent2()
    {
        $exitStatusInfo = $this->object->signalEvent("nethgui-test");
        $this->assertEquals(0, $exitStatusInfo->getExitStatus());
    }

    public function testGetMapAdapter()
    {
        $adapter = $this->object->getMapAdapter(
            array($this, 'readCallback'), array($this, 'writeCallback'), array(
            array('testdb', 'testkey1'),
            array('testdb', 'testkey2', 'testpropA'),
            array('testdb', 'testkey3', 'testpropB'),
            )
        );
        $this->assertInstanceOf('Nethgui_Adapter_AdapterInterface', $adapter);
    }

    public function readCallback($key1, $propA, $propB)
    {
        return implode(',', array($key1, $propA, $propB));
    }

    public function writeCallback($value)
    {
        return explode(',', $value);
    }

    public function testGetIdentityAdapter()
    {
        $this->assertInstanceOf('Nethgui_Adapter_AdapterInterface', $this->object->getIdentityAdapter('testdb', 'testkey'));
        $this->assertInstanceOf('ArrayAccess', $this->object->getIdentityAdapter('testdb', 'testkey', NULL, ','));
    }

}
