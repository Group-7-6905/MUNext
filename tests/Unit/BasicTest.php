<?php

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testPasswordHashing()
    {
        $password = 'testPassword123';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
    }
    
    public function testEmailValidation()
    {
        $validEmail = 'user@example.com';
        $invalidEmail = 'not-an-email';
        
        $this->assertNotFalse(
            filter_var($validEmail, FILTER_VALIDATE_EMAIL)
        );
        $this->assertFalse(
            filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)
        );
    }
}