<?php

/**
 * Open book core
 *
 * @author Mechemsi
 */

declare(strict_types=1);

namespace Mechemsi\Proto\Attribute\ApiPlatform;

use OpenApi\Annotations as OAA;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiSuccessResponseAttribute  extends OAA\Response
{
    public function __construct(
        string $description = null,
    ) {
        $content = new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                )
            ],
            type: 'object'
        );

        parent::__construct([
            'response' => 200,
            'description' => $description ?? 'Success response',
            'value' => $this->combine($content),
        ]);
    }
}
