<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class MicroPostValidator
{
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('title', [
            new Assert\NotBlank(message: 'Title cannot be empty.'),
            new Assert\Length(
                min: 3,
                max: 255,
                minMessage: 'Title must be at least {{ limit }} characters.',
                maxMessage: 'Title cannot exceed {{ limit }} characters.',
            ),
        ]);

        $metadata->addPropertyConstraints('text', [
            new Assert\NotBlank(message: 'Text cannot be empty.'),
            new Assert\Length(
                min: 5,
                max: 500,
                minMessage: 'Text must be at least {{ limit }} characters.',
                maxMessage: 'Text cannot exceed {{ limit }} characters.',
            ),
        ]);
    }
}