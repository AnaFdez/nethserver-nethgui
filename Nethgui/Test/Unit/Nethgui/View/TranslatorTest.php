<?php
namespace Nethgui\Test\Unit\Nethgui\View;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-24 at 09:08:37.
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Nethgui\View\Translator
     */
    protected $object;
    protected $module;

    protected function setUp()
    {
        $this->object = new \Nethgui\View\Translator('xy',
            function ($catalog) {
            return $catalog;
        }, array('Nethgui_Basic'));
        $this->object->setPhpWrapper(new TranslatorTestPhpWrapper(__CLASS__));
        $parent = new TranslatorTestModule(array('Nethgui_ParentCatalog1', 'Nethgui_ParentCatalog2'));
        $this->module = new TranslatorTestModule('Nethgui_ModuleCatalog');
        $this->module->setParent($parent);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructor1()
    {
        $o = new \Nethgui\View\Translator('xy', NULL, array('Nethgui_Basic'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongCatalog()
    {
        $o = new \Nethgui\View\Translator('xy',
            function ($f) {
            return $f;
        }, array('Nethgui_P/q'));
        $o->translate($this->module, 'LABEL');
    }

    /**
     */
    public function testTranslate1()
    {
        $this->assertEquals('SYM_TRANSLATED1',
            $this->object->translate($this->module, 'SYM_UNTRANSLATED'));
    }

    /**
     */
    public function testTranslate2()
    {
        $this->assertEquals('SYM_TRANSLATED2',
            $this->object->translate($this->module, 'SYM_ENGLISH_FALLBACK'));
        $this->assertEquals('NX',
            $this->object->translate($this->module, 'NX', array(), 'en'));
    }

    /**
     */
    public function testTranslate3()
    {
        $this->assertEquals('SYM_TRANSLATED3 A B C',
            $this->object->translate($this->module, 'SYM_INTERPOLATE1',
                array('A', 'B', 'C')));
        $this->assertEquals('SYM_TRANSLATED4 A B C',
            $this->object->translate($this->module, 'SYM_INTERPOLATE2',
                array(':x' => 'A', ':y' => 'B', ':z' => 'C')));
    }

    public function testTranslate4()
    {
        $this->assertEquals('NX',
            $this->object->translate($this->module, 'NX', array(), ''));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTranslate5()
    {
        $this->object->translate($this->module, 1000, array(), '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTranslate6()
    {
        $this->object->translate($this->module, 'LABEL', array(), 'a.');
    }

    public function testTranslate7()
    {
        $this->assertEquals('SYM_TRANSLATED7a',
            $this->object->translate($this->module, 'FROM_PARENT_CATALOG1'));
        $this->assertEquals('SYM_TRANSLATED7b',
            $this->object->translate($this->module, 'FROM_PARENT_CATALOG2'));
    }

    /**
     * @covers Nethgui\View\Translator::setPhpWrapper
     */
    public function testSetPhpWrapper()
    {
        return $this->assertSame($this->object,
                $this->object->setPhpWrapper(new \Nethgui\Utility\PhpWrapper(__CLASS__)));
    }

    /**
     * @covers Nethgui\View\Translator::getLog
     */
    public function testGetLog()
    {
        return $this->assertInstanceOf('\Nethgui\Log\LogInterface',
                $this->object->getLog());
    }

    /**
     * @covers Nethgui\View\Translator::setLog
     */
    public function testSetLog()
    {
        $log = new \Nethgui\Log\Nullog();
        $this->assertSame($log, $this->object->setLog($log)->getLog());
    }

    /**
     * @covers Nethgui\View\Translator::getLanguageCode     
     */
    public function testGetLanguageCode()
    {
        $this->assertEquals('xy', $this->object->getLanguageCode());
    }

}

class TranslatorTestPhpWrapper extends \Nethgui\Utility\PhpWrapper
{

    public function __construct($identifier = __CLASS__)
    {
        parent::__construct($identifier);
    }

    public function phpInclude($filePath, $vars)
    {
        switch ($filePath) {
            case 'Nethgui\Language\xy\Nethgui_Basic':
                $vars['L']['SYM_UNTRANSLATED'] = 'SYM_TRANSLATED1';
                break;
            case 'Nethgui\Language\xy\Nethgui_ModuleCatalog':
                $vars['L']['SYM_INTERPOLATE1'] = 'SYM_TRANSLATED3 ${0} ${1} ${2}';
                $vars['L']['SYM_INTERPOLATE2'] = 'SYM_TRANSLATED4 :x :y :z';
                break;
            case 'Nethgui\Language\en\Nethgui_ModuleCatalog':
                $vars['L']['SYM_ENGLISH_FALLBACK'] = 'SYM_TRANSLATED2';
                break;
            case 'Nethgui\Language\xy\Nethgui_ParentCatalog1':
                $vars['L']['FROM_PARENT_CATALOG1'] = 'SYM_TRANSLATED7a';
                break;
            case 'Nethgui\Language\xy\Nethgui_ParentCatalog2':
                $vars['L']['FROM_PARENT_CATALOG2'] = 'SYM_TRANSLATED7b';
                break;
            default:
                return parent::phpInclude($filePath, $vars);
        }
        return TRUE;
    }

}

class TranslatorTestModule implements \Nethgui\Module\ModuleInterface, \Nethgui\Module\ModuleAttributesInterface
{
    private $parent;

    public function __construct($catalog = 'Nethgui_ModuleCatalog')
    {
        $this->catalog = $catalog;
    }

    public function getAttributesProvider()
    {
        return $this;
    }

    public function getIdentifier()
    {
        return 'TranslatorTestModule';
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function initialize()
    {
        // NOOP
    }

    public function isInitialized()
    {
        return FALSE;
    }

    public function setParent(\Nethgui\Module\ModuleInterface $parentModule)
    {
        $this->parent = $parentModule;
        return $this;
    }

    public function getCategory()
    {
        return 'Cat';
    }

    public function getDescription()
    {
        return 'Description';
    }

    public function getLanguageCatalog()
    {
        return $this->catalog;
    }

    public function getMenuPosition()
    {
        return 1;
    }

    public function getTags()
    {
        return 'Tag';
    }

    public function getTitle()
    {
        return 'Title';
    }

}