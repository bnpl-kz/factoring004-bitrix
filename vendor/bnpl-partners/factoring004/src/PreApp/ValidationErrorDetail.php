<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;

/**
 * @psalm-immutable
 */
class ValidationErrorDetail implements ArrayInterface
{
    /**
     * @var string
     */
    private $error;
    /**
     * @var string
     */
    private $field;

    public function __construct(string $error, string $field)
    {
        $this->error = $error;
        $this->field = $field;
    }

    /**
     * @param array<string, string> $detail
     * @psalm-param array{error: string, field: string} $detail
     */
    public static function createFromArray($detail): ValidationErrorDetail
    {
        return new ValidationErrorDetail($detail['error'], $detail['field']);
    }

    /**
     * @param array<string, string>[] $details
     *
     * @psalm-param array{error: string, field: string}[] $details
     *
     * @return \BnplPartners\Factoring004\PreApp\ValidationErrorDetail[]
     */
    public static function createMany($details): array
    {
        return array_map([ValidationErrorDetail::class, 'createFromArray'], $details);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{error: string, field: string}
     */
    public function toArray(): array
    {
        return [
            'error' => $this->getError(),
            'field' => $this->getField(),
        ];
    }
}
