<?php
/**
 * JBZoo Data
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Data
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Data
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\Data;

/**
 * Class phpArray
 * @package JBZoo\Data
 */
class PhpArray extends Base
{
    const TAB = '    ';

    /**
     * Class constructor
     * @param array|string $data The data array
     */
    public function __construct($data = array())
    {
        if ($data && is_string($data) && file_exists($data)) {
            $data = $this->decode($data);
        }

        parent::__construct($data ? (array)$data : array());
    }

    /**
     * Utility Method to unserialize the given data
     * @param string $string
     * @return mixed
     */
    protected function decode($string)
    {
        return include $string;
    }

    /**
     * Utility Method to serialize the given data
     * @param mixed $data The data to serialize
     * @return string The serialized data
     */
    protected function encode($data)
    {
        $data = array(
            '<?php',
            '',
            'return ' . $this->render($data, 0) . ';',
        );

        return implode(Base::LE, $data);
    }

    /**
     * @param array $array
     * @param int   $depth
     * @return string
     */
    protected function render($array, $depth = 0)
    {
        $data = (array)$array;

        $string = 'array(' . Base::LE;

        $depth++;
        foreach ($data as $key => $val) {
            $string .= $this->getIndent($depth) . $this->quoteWrap($key) . ' => ';

            if (is_array($val) || is_object($val)) {
                $string .= $this->render($val, $depth) . ',' . Base::LE;
            } else {
                $string .= $this->quoteWrap($val) . ',' . Base::LE;
            }
        }

        $depth--;
        $string .= $this->getIndent($depth) . ')';

        return $string;
    }

    /**
     * @param $depth
     * @return string
     */
    protected function getIndent($depth)
    {
        return str_repeat(self::TAB, $depth);
    }

    /**
     * @param $var
     * @return string
     */
    protected function quoteWrap($var)
    {
        $type = strtolower(gettype($var));

        switch ($type) {
            case 'string':
                return "'" . str_replace("'", "\\'", $var) . "'";

            case 'null':
                return "null";

            case 'boolean':
                return $var ? 'true' : 'false';

            //TODO: handle other variable types.. ( objects? )
            case 'integer':
            case 'double':
        }

        return $var;
    }
}
