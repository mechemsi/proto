<?php

/**
 * Open book core
 *
 * @author Mechemsi
 */

declare(strict_types=1);

namespace Mechemsi\Proto\Attribute\ApiPlatform\Proto;

use Google\Protobuf\Internal\FieldDescriptor;
use Google\Protobuf\Internal\DescriptorPool;
use Google\Protobuf\Internal\GPBLabel;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\Message;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ProtoToContent extends OA\JsonContent
{
    public function __construct(
        private string $messageClassName
    ) {
        $properties = $this->generateProperties();

        parent::__construct(
            properties: $properties,
            type: 'object'
        );
    }

    private function generateProperties(): array
    {
        $pool = DescriptorPool::getGeneratedPool();
        $desc = $pool->getDescriptorByClassName(get_class($this->initGeneratedProtoClass()));

        if (is_null($desc)) {
            throw new \InvalidArgumentException(
                $this->messageClassName . " is not found in descriptor pool. " .
                'Only generated classes may derive from Message.'
            );
        }

        return $this->getProperties($desc->getField());
    }

    private function initGeneratedProtoClass(): Message
    {
        try {
            $reflectionClass = new \ReflectionClass($this->messageClassName);

            /** @var Message $message */
            $message = $reflectionClass->newInstance();
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException(
                $this->messageClassName . " is not found in descriptor pool. " .
                'Only generated classes may derive from Message.'
            );
        }

        return $message;
    }

    private function getProperties(array $fields, bool $singleLevel = false): array
    {
        $properties = [];

        /** @var FieldDescriptor $field */
        foreach ($fields as $field) {
            $properties[] = $this->fieldProperty($field, $singleLevel);
        }

        return $properties;
    }

    private function fieldProperty(FieldDescriptor $field, bool $singleLevel = false): ?OA\Property
    {
        $property = null;
        if ($field->isMap()) {
            $property = new OA\Property(
                property: $field->getName(),
                properties: !$singleLevel ? [$this->mapFieldItems($field->getMessageType()->getField())]: [],
                type: 'object'
            );
        } else {
            if ($field->getLabel() === GPBLabel::REPEATED) {
                $property = new OA\Property(
                    property: $field->getName(),
                    type: 'array',
                    items: $this->repeatedFieldItems($field)
                );
            } else {
                switch ($field->getType()) {
                    case GPBType::MESSAGE:
                    case GPBType::GROUP:
                        if ($field->getMessageType()->getClass() === $this->messageClassName) {
                            $property = new OA\Property(
                                property: $field->getName(),
                                properties: [],
                                type: 'object',
                            );
                            break;
                        }
                        $property = new OA\Property(
                            property: $field->getName(),
                            properties: $this->getProperties($field->getMessageType()->getField()),
                            type: 'object',
                        );
                        break;
                    case GPBType::BOOL:
                        $property = new OA\Property(
                            property: $field->getName(),
                            type: 'boolean'
                        );
                        break;
                    case GPBType::FLOAT:
                    case GPBType::DOUBLE:
                        $property = new OA\Property(
                            property: $field->getName(),
                            type: 'number'
                        );
                        break;
                    case GPBType::INT32:
                    case GPBType::INT64:
                        $property = new OA\Property(
                            property: $field->getName(),
                            type: 'integer'
                        );
                        break;
                    case GPBType::STRING:
                        $property = new OA\Property(
                            property: $field->getName(),
                            type: 'string'
                        );
                        break;
                }
            }
        }

        return $property;
    }

    private function repeatedFieldItems(FieldDescriptor $field): ?OA\Items
    {
        $items = null;
        switch ($field->getType()) {
            case GPBType::MESSAGE:
            case GPBType::GROUP:
                $items = new OA\Items(
                    properties: $this->getProperties($field->getMessageType()->getField()),
                );
                break;
            case GPBType::BOOL:
                $items = new OA\Items(
                    type: 'boolean'
                );
                break;
            case GPBType::FLOAT:
            case GPBType::DOUBLE:
                $items = new OA\Items(
                    type: 'number'
                );
                break;
            case GPBType::INT32:
            case GPBType::INT64:
                $items = new OA\Items(
                    type: 'integer'
                );
                break;
            case GPBType::STRING:
                $items = new OA\Items(
                    type: 'string'
                );
                break;
        }

        return $items;
    }

    private function mapFieldItems($fields): ?OA\Property
    {
        $items = null;
        foreach ($fields as $i => $field) {
            if ($field->getName() === 'value') {
                switch ($field->getType()) {
                    case GPBType::MESSAGE:
                    case GPBType::GROUP:
                        $singleLevel = false;
                        if ($field->getMessageType()->getClass() === $this->messageClassName) {
                            $singleLevel = true;
                        }
                        $items = new OA\Property(
                            property: 'key',
                            properties: $this->getProperties($field->getMessageType()->getField(), $singleLevel),
                            type: 'object'
                        );
                        break;
                    case GPBType::BOOL:
                        $items = new OA\Property(
                            property: 'key',
                            type: 'boolean'
                        );
                        break;
                    case GPBType::FLOAT:
                    case GPBType::DOUBLE:
                        $items = new OA\Property(
                            property: 'key',
                            type: 'number'
                        );
                        break;
                    case GPBType::INT32:
                    case GPBType::INT64:
                        $items = new OA\Property(
                            property: 'key',
                            type: 'integer'
                        );
                        break;
                    case GPBType::STRING:
                        $items = new OA\Property(
                            property: 'key',
                            type: 'string'
                        );
                        break;
                }
            }
        }

        return $items;
    }
}
