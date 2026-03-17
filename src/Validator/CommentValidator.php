<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CommentValidator
{
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('text', [
            new Assert\NotBlank(message: 'Comment cannot be empty.'),
            new Assert\Length(
                min: 3,
                max: 500,
                minMessage: 'Comment must be at least {{ limit }} characters.',
                maxMessage: 'Comment cannot exceed {{ limit }} characters.',
            ),
        ]);
    }
}