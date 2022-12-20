<?php

namespace BnplPartners\Factoring004;

use PHPUnit\Framework\TestCase;

/**
 * @method object createStub(string $class)
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @param string $name
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function __call($name, array $arguments)
    {
        if ($name === 'createStub') {
            return $this->createMock(...$arguments);
        }

        throw new \BadMethodCallException('Method ' . $name . ' does not exist');
    }
}
