<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;
use Flusher;
/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Sets a protected property on a given object via reflection
     *
     * @param $object   - instance in which protected value is being modified
     * @param $property - property on instance being modified
     * @param $value    - new value of the property being modified
     *
     * @return void
     * @throws ReflectionException
     */
    public function setProtectedProperty($object, $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Gets a protected property on a given object via reflection
     *
     * @param $object
     * @param $property
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function getProtectedProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        Flusher::open();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        Flusher::rollback();
        $this->clear();

        parent::tearDown();
    }

    /**
     * Flush
     */
    protected function flush(): void
    {
        Flusher::flush();
        Flusher::commit();
    }

    /**
     * clear
     *
     * @param null $objectName
     */
    protected function clear($objectName = null): void
    {
        Flusher::clear($objectName);
    }
}
