<?php

namespace App\Support\Cache;

use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

class SiteCacheValueSerializer
{
    use SerializesAndRestoresModelIdentifiers;

    private const TYPE_KEY = '__site_cache_type';

    private const TYPE_MODEL = 'model';

    private const TYPE_OBJECT = 'object';

    private const TYPE_STD_CLASS = 'std_class';

    public function prepare(mixed $value): mixed
    {
        if ($value instanceof QueueableCollection || $value instanceof QueueableEntity) {
            return [
                self::TYPE_KEY => self::TYPE_MODEL,
                'value' => $this->getSerializedPropertyValue($value),
            ];
        }

        if (is_array($value)) {
            $prepared = [];

            foreach ($value as $key => $item) {
                $prepared[$key] = $this->prepare($item);
            }

            return $prepared;
        }

        if ($value instanceof stdClass) {
            return [
                self::TYPE_KEY => self::TYPE_STD_CLASS,
                'properties' => $this->prepare((array) $value),
            ];
        }

        if (is_object($value)) {
            return $this->prepareObject($value);
        }

        return $value;
    }

    public function restore(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $type = $value[self::TYPE_KEY] ?? null;

        if ($type === self::TYPE_MODEL) {
            return $this->getRestoredPropertyValue($value['value'] ?? null);
        }

        if ($type === self::TYPE_OBJECT) {
            return $this->restoreObject($value);
        }

        if ($type === self::TYPE_STD_CLASS) {
            $object = new stdClass();

            foreach ($this->restore($value['properties'] ?? []) as $key => $item) {
                $object->{$key} = $item;
            }

            return $object;
        }

        $restored = [];

        foreach ($value as $key => $item) {
            $restored[$key] = $this->restore($item);
        }

        return $restored;
    }

    private function prepareObject(object $value): array
    {
        $reflection = new ReflectionClass($value);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->isInitialized($value)) {
                continue;
            }

            if (method_exists($property, 'isVirtual') && $property->isVirtual()) {
                continue;
            }

            $property->setAccessible(true);

            $properties[$this->propertyKey($property, $reflection->getName())] = $this->prepare(
                $property->getValue($value)
            );
        }

        return [
            self::TYPE_KEY => self::TYPE_OBJECT,
            'class' => $reflection->getName(),
            'properties' => $properties,
        ];
    }

    private function restoreObject(array $value): object
    {
        $class = $value['class'] ?? null;
        $serializedProperties = is_array($value['properties'] ?? null) ? $value['properties'] : [];

        $reflection = new ReflectionClass($class);
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $key = $this->propertyKey($property, $reflection->getName());

            if (! array_key_exists($key, $serializedProperties)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($object, $this->restore($serializedProperties[$key]));
        }

        return $object;
    }

    private function propertyKey(ReflectionProperty $property, string $class): string
    {
        $name = $property->getName();

        if ($property->isPrivate()) {
            return "\0{$class}\0{$name}";
        }

        if ($property->isProtected()) {
            return "\0*\0{$name}";
        }

        return $name;
    }
}
