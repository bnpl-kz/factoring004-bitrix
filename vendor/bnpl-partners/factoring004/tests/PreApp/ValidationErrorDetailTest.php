<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use PHPUnit\Framework\TestCase;

class ValidationErrorDetailTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $actual = ValidationErrorDetail::createFromArray(['error' => 'something went wrong', 'field' => 'expiresAt']);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateMany(): void
    {
        $actual = ValidationErrorDetail::createMany([
            ['error' => 'something went wrong', 'field' => 'expiresAt'],
            ['error' => 'an error occurred', 'field' => 'deliveryDate'],
        ]);

        $expected = [
            ValidationErrorDetail::createFromArray(['error' => 'something went wrong', 'field' => 'expiresAt']),
            ValidationErrorDetail::createFromArray(['error' => 'an error occurred', 'field' => 'deliveryDate']),
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetField(): void
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals('expiresAt', $detail->getField());

        $detail = new ValidationErrorDetail('something went wrong', 'deliveryDate');
        $this->assertEquals('deliveryDate', $detail->getField());
    }

    public function testGetError(): void
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals('something went wrong', $detail->getError());

        $detail = new ValidationErrorDetail('an error occurred', 'deliveryDate');
        $this->assertEquals('an error occurred', $detail->getError());
    }

    public function testToArray(): void
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals(['error' => 'something went wrong', 'field' => 'expiresAt'], $detail->toArray());

        $detail = new ValidationErrorDetail('an error occurred', 'deliveryDate');
        $this->assertEquals(['error' => 'an error occurred', 'field' => 'deliveryDate'], $detail->toArray());
    }
}

