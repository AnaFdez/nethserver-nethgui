<?php
/**
 * @package Renderer
 * @author Davide Principi <davide.principi@nethesis.it>
 */

namespace Nethgui\Renderer;

/**
 * Transform a view into a string.
 *
 * @see WidgetInterface
 * @see http://en.wikipedia.org/wiki/Decorator_pattern
 * @package Renderer
 */
abstract class AbstractRenderer extends ReadonlyView
{

    abstract protected function render();

    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $ex) {
            $this->getLog()->exception($ex, TRUE);
        }
        return '';
    }

    /**
     * Convert the given hash to the array format accepted from UI widgets as
     * "datasource".
     *
     * @param array $h
     * @return array
     */
    public static function hashToDatasource($H)
    {
        $D = array();

        foreach ($H as $k => $v) {
            if (is_array($v)) {
                $D[] = array(self::hashToDatasource($v), $k);
            } elseif (is_string($v)) {
                $D[] = array($k, $v);
            }
        }

        return $D;
    }

}
