<?php

namespace Flagbit\Plantuml\TokenReflection;

use TokenReflection\IReflectionClass;

class ClassWriter extends WriterAbstract
{
    /**
     * @var \Flagbit\Plantuml\TokenReflection\ConstantWriter
     */
    private $constantWriter;

    /**
     * @var \Flagbit\Plantuml\TokenReflection\PropertyWriter
     */
    private $propertyWriter;

    /**
     * @var \Flagbit\Plantuml\TokenReflection\MethodWriter
     */
    private $methodWriter;

    public function __construct()
    {
        $this->setIndent('');
    }

    /**
     * @param \Flagbit\Plantuml\TokenReflection\ConstantWriter $constantWriter
     */
    public function setConstantWriter($constantWriter)
    {
        $this->constantWriter = $constantWriter;
    }

    /**
     * @param \Flagbit\Plantuml\TokenReflection\MethodWriter $methodWriter
     */
    public function setMethodWriter($methodWriter)
    {
        $this->methodWriter = $methodWriter;
    }

    /**
     * @param \Flagbit\Plantuml\TokenReflection\PropertyWriter $propertyWriter
     */
    public function setPropertyWriter($propertyWriter)
    {
        $this->propertyWriter = $propertyWriter;
    }

    /**
     * @param \TokenReflection\IReflectionClass $class
     *
     * @return string
     */
    public function writeElement(IReflectionClass $class)
    {
        $classString = $this->formatLine(
            $this->writeAbstract($class) . $this->writeObjectType($class) . ' ' . $this->formatClassName(
                $class->getName()
            ) . ' {'
        );

        if ($this->constantWriter) {
            $classString .= $this->constantWriter->writeElements($class->getConstantReflections());
        }

        if ($this->propertyWriter) {
            $classString .= $this->propertyWriter->writeElements($class->getProperties());
        }

        if ($this->methodWriter) {
            $classString .= $this->methodWriter->writeElements($class->getMethods());
        }

        $classString .= $this->formatLine('}');

        if ($class->getParentClassName()) {
            $classString .= $this->formatLine(
                $this->writeObjectType($class) . ' ' . $this->formatClassName($class->getName()) . ' extends '
                . $this->formatClassName(
                    $class->getParentClassName()
                )
            );
        }

        if ($interfaceNames = $class->getInterfaceNames()) {
            if ($class->getParentClass()) {
                $interfaceNames = array_diff($interfaceNames, $class->getParentClass()->getInterfaceNames());
            }

            foreach ($interfaceNames as $interfaceName) {
                $classString .= $this->formatLine(
                    $this->writeObjectType($class) . ' ' . $this->formatClassName($class->getName()) . ' implements '
                    . $this->formatClassName(
                        $interfaceName
                    )
                );
            }
        }

        return $classString;
    }

    /**
     * @param IReflectionClass $class
     *
     * @return string
     */
    private function writeAbstract(IReflectionClass $class)
    {
        $return = '';
        if (true === $class->isAbstract() && false === $class->isInterface()) {
            $return = 'abstract ';
        }
        return $return;
    }

    /**
     * @param IReflectionClass $class
     *
     * @return string
     */
    private function writeObjectType(IReflectionClass $class)
    {
        $return = 'class';
        if (true === $class->isInterface()) {
            $return = 'interface';
        }
        return $return;
    }
}
