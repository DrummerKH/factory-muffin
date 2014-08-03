<?php

namespace League\FactoryMuffin\Generators;

use League\FactoryMuffin\Exceptions\MethodNotFoundException;
use League\FactoryMuffin\Facade as FactoryMuffin;

/**
 * Class Call.
 *
 * @package League\FactoryMuffin\Generator
 * @author  Zizaco <zizaco@gmail.com>
 * @author  Scott Robertson <scottymeuk@gmail.com>
 * @author  Graham Campbell <graham@mineuk.com>
 * @license <https://github.com/thephpleague/factory-muffin/blob/master/LICENSE> MIT
 */
class Call extends Base
{
    /**
     * Generate, and return the attribute.
     *
     * @throws \League\FactoryMuffin\Exceptions\MethodNotFoundException
     *
     * @return mixed
     */
    public function generate()
    {
        $method = substr($this->kind, 5);
        $args = array();

        if (strstr($method, '|')) {
            $parts = explode('|', $method);
            $method = array_shift($parts);

            if ($parts[0] === 'factory' && count($parts) > 1) {
                $args[] = $this->factory($parts[1]);
            } else {
                $args[] = FactoryMuffin::generateAttr(implode('|', $parts), $this->object);
            }
        }

        return $this->execute($method, $args);
    }

    /**
     * Call a static method on the model.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    private function execute($method, $args)
    {
        if (method_exists($model = get_class($this->object), $method)) {
            return call_user_func_array(array($model, $method), $args);
        }

        throw new MethodNotFoundException($model, $method);
    }
}
