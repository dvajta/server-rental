<?php

namespace tests\acceptance\auth;

use frontend\tests\AcceptanceTester;
use common\fixtures\UserFixture;


class AuthCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/site/index');
    }

    public function _fixtures()
    {
        return [
            'users' => UserFixture::class,
        ];
    }

    public function testAuthPost(AcceptanceTester $I)
    {
        $I->wantTo('Action (POST) with valid token');
        $I->selectOption('#authform-method', 'POST');
        $I->fillField('#authform-token', 'valid_token_value');
        $I->fillField('#authform-json', '{"name": "John","age": 30,"email": "john@example.com","phone": "+1 555-555-1234","pets": ["dog", "cat"]}');
        $I->click('#auth-form button[type=submit]');
        $I->seeResponseCodeIs(200);
    }

    public function testAuthGet(AcceptanceTester $I)
    {
        $I->wantTo('Action (GET) with valid token');
        $I->selectOption('#authform-method', 'GET');
        $I->fillField('#authform-token', 'valid_token_value');
        $I->fillField('#authform-json', '{"name": "John","age": 30,"email": "john@example.com","phone": "+1 555-555-1234","pets": ["dog", "cat"]}');
        $I->click('#auth-form button[type=submit]');
        $I->seeResponseCodeIs(200);
    }

    public function testAuthPostInvalidToken(AcceptanceTester $I)
    {
        $I->wantTo('Action (POST) with invalid token');
        $I->selectOption('#authform-method', 'POST');
        $I->fillField('#authform-token', 'invalid_token_value');
        $I->fillField('#authform-json', '{"name": "John","age": 30,"email": "john@example.com","phone": "+1 555-555-1234","pets": ["dog", "cat"]}');
        $I->click('#auth-form button[type=submit]');
        $I->seeResponseCodeIs(400);
    }

    public function testAuthGetInvalidToken(AcceptanceTester $I)
    {
        $I->wantTo('Action (GET) with invalid token');
        $I->selectOption('#authform-method', 'GET');
        $I->fillField('#authform-token', 'invalid_token_value');
        $I->fillField('#authform-json', '{"name": "John","age": 30,"email": "john@example.com","phone": "+1 555-555-1234","pets": ["dog", "cat"]}');
        $I->click('#auth-form button[type=submit]');
        $I->seeResponseCodeIs(400);
    }
}