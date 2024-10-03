<?php

/**
 * Open book core
 *
 * @author Mechemsi
 */

declare(strict_types=1);

namespace Mechemsi\Proto\Attribute\ApiPlatform;

use Mechemsi\Proto\Attribute\ApiPlatform\Proto\ProtoToContent;
use Attribute;
use Mechemsi\FormHandler\Proto\FormErrors;
use OpenApi\Annotations as OAA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ApiFormErrorResponseAttribute extends OAA\Response
{
    public function __construct(
        string $description = null,
    ) {
        $content = new ProtoToContent(FormErrors::class);

        parent::__construct([
            'response' => 400,
            'description' => $description ?? 'Form error response',
            'value' => $this->combine($content),
        ]);
    }
}
