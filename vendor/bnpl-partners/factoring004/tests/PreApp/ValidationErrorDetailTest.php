<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractTestCase;

class ValidationErrorDetailTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $actual = ValidationErrorDetail::createFromArray(['error' => 'something went wrong', 'field' => 'expiresAt']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testCreateMany()
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

    /**
     * @return void
     */
    public function testGetField()
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals('expiresAt', $detail->getField());

        $detail = new ValidationErrorDetail('something went wrong', 'deliveryDate');
        $this->assertEquals('deliveryDate', $detail->getField());
    }

    /**
     * @return void
     */
    public function testGetError()
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals('something went wrong', $detail->getError());

        $detail = new ValidationErrorDetail('an error occurred', 'deliveryDate');
        $this->assertEquals('an error occurred', $detail->getError());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $detail = new ValidationErrorDetail('something went wrong', 'expiresAt');
        $this->assertEquals(['error' => 'something went wrong', 'field' => 'expiresAt'], $detail->toArray());

        $detail = new ValidationErrorDetail('an error occurred', 'deliveryDate');
        $this->assertEquals(['error' => 'an error occurred', 'field' => 'deliveryDate'], $detail->toArray());
    }
}

