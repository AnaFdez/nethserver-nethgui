<?php
namespace Nethgui\Test\Unit\Nethgui\Module;

/**
 * @covers Nethgui\Module\Composite
 */
class CompositeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Nethgui\Module\Composite
     */
    protected $object;

    protected function setUp()
    {
        $platform = $this->getMock('Nethgui\System\PlatformInterface');

        $this->object = new ConcreteCompositeModule1();
        $this->object->setPlatform($platform);
    }

 
    public function testAddChildInitialized()
    {
        $this->object->initialize();

        $mockModule1 = $this->getMockBuilder('\Nethgui\Controller\AbstractController')
            ->getMock();

        $mockModule1->expects($this->once())
            ->method('isInitialized')
            ->will($this->returnValue(FALSE));

        $mockModule1->expects($this->once())
            ->method('initialize');

        $mockModule1->expects($this->once())
            ->method('setParent');

        $this->object->addChild($mockModule1);
    }

    public function testInitialize()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetChildren().
     */
    public function testGetChildren()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testBind().
     */
    public function testBind()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidate().
     */
    public function testValidate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testProcess().
     */
    public function testProcess()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPrepareView().
     */
    public function testPrepareView()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetHostConfiguration().
     */
    public function testSetHostConfiguration()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

class ConcreteCompositeModule1 extends \Nethgui\Module\Composite
{
    
}
