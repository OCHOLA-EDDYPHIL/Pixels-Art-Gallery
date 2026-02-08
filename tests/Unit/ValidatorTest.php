<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Utils\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidEmail(): void
    {
        $this->assertTrue(Validator::email('test@example.com'));
        $this->assertFalse(Validator::email('not-an-email'));
    }

    public function testPasswordValidation(): void
    {
        $errors = Validator::password('weak');
        $this->assertNotEmpty($errors);

        $errorsStrong = Validator::password('Strong123!');
        $this->assertEmpty($errorsStrong);
    }

    public function testUrlValidation(): void
    {
        $this->assertTrue(Validator::url('https://example.com/path'));
        $this->assertFalse(Validator::url('javascript:alert(1)'));
        $this->assertFalse(Validator::url('not-a-url'));
    }
}
