<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function test_password_is_hashed_and_hidden_from_serialized_users(): void
    {
        $user = new User;

        $this->assertSame('hashed', $user->getCasts()['password']);
        $this->assertContains('password', $user->getHidden());
    }
}
