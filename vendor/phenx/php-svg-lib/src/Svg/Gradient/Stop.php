class Stop
{
    /**
     * The offset of the stop, as a percentage or a decimal value.
     *
     * @var float
     */
    public $offset;

    /**
     * The color of the stop, in hexadecimal or RGB format.
     *
     * @var string
     */
    public $color;

    /**
     * The opacity of the stop, as a decimal value between 0 and 1.
     *
     * @var float
     */
    public $opacity = 1.0;

    /**
     * Constructor for the Stop class.
     *
     * @param float  $offset  The offset of the stop.
     * @param string $color   The color of the stop.
     * @param float  $opacity The opacity of the stop.
     */
    public function __construct($offset, $color, $opacity = 1.0)
    {
        $this->offset = $offset;
        $this->color = $color;
        $this->opacity = $opacity;
    }
}class Stop
{
    /**
     * The offset of the stop, as a percentage or a decimal value.
     *
     * @var float
     */
    private $offset;

    /**
     * The color of the stop, in hexadecimal or RGB format.
     *
     * @var string
     */
    private $color;

    /**
     * The opacity of the stop, as a decimal value between 0 and 1.
     *
     * @var float
     */
    private $opacity;

    /**
     * Constructor for the Stop class.
     *
     * @param float  $offset  The offset of the stop.
     * @param string $color   The color of the stop.
     * @param float  $opacity The opacity of the stop.
     */
    public function __construct($offset, $color, $opacity = 1.0)
    {
        $this->setOffset($offset);
        $this->setColor($color);
        $this->setOpacity($opacity);
    }

    /**
     * Sets the offset of the stop.
     *
     * @param float $offset The offset of the stop.
     */
    public function setOffset($offset)
    {
        if (!is_numeric($offset)) {
            throw new \InvalidArgumentException('Offset must be a numeric value');
        }
        $this->offset = $offset;
    }

    /**
     * Sets the color of the stop.
     *
     * @param string $color The color of the stop.
     */
    public function setColor($color)
    {
        if (!is_string($color)) {
            throw new \InvalidArgumentException('Color must be a string');
        }
        $this->color = $color;
    }

    /**
     * Sets the opacity of the stop.
     *
     * @param float $opacity The opacity of the stop.
     */
    public function setOpacity($opacity)
    {
        if (!is_numeric($opacity) || $opacity < 0 || $opacity > 1) {
            throw new \InvalidArgumentException('Opacity must be a numeric value between 0 and 1');
        }
        $this->opacity = $opacity;
    }

    /**
     * Gets the offset of the stop.
     *
     * @return float The offset of the stop.
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Gets the color of the stop.
     *
     * @return string The color of the stop.
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Gets the opacity of the stop.
     *
     * @return float The opacity of the stop.
     */
    public function getOpacity()
    {
        return $this->opacity;
    }
}<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Gradient;

class Stop
{
    public $offset;
    public $color;
    public $opacity = 1.0;
}
