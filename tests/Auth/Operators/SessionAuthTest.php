<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J), Lev Seleznev
 */

namespace Spiral\Tests\Auth\Operators;

use Spiral\Auth\Hashing\PasswordHasher;
use Spiral\Auth\Middlewares\AuthMiddleware;
use Spiral\Auth\Operators\HttpOperator;
use Spiral\Auth\Operators\SessionOperator;
use Spiral\Tests\HttpTest;
use TestApplication\Database\User;

class SessionAuthTest extends HttpTest
{
    public function testCreateToken()
    {
        $hasher = new PasswordHasher();

        $user = new User();
        $user->username = 'username';
        $user->password_hash = $hasher->hash('password');
        $user->save();

        $this->http->setEndpoint(function () use ($user) {
            $this->auth->init(
                $this->tokens->createToken('session', $user)
            );

            $this->assertTrue($this->auth->hasToken());
            $this->assertTrue($this->auth->hasUser());
            $this->assertFalse($this->auth->isClosed());
            $this->assertTrue($this->auth->isAuthenticated());
            $this->assertSame($user->primaryKey(), $this->auth->getUser()->primaryKey());

            //Cached
            $this->assertSame($user->primaryKey(), $this->auth->getUser()->primaryKey());

            $token = $this->auth->getToken();

            $this->assertSame('session-auth', $token->getValue());
            $this->assertInstanceOf(SessionOperator::class, $token->getOperator());
        });

        $this->http->pushMiddleware(AuthMiddleware::class);
        $this->get('/');
    }
}