<?php


namespace tests\acceptance\auth;

use frontend\tests\AcceptanceTester;
use common\fixtures\UserFixture;


class UpdateAuthCest
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

    public function testUpdateAuthPost(AcceptanceTester $I)
    {
        $I->wantTo('Action (POST) with valid token');
        $I->selectOption('#authupdateform-method', 'POST');
        $I->fillField('#authupdateform-token', 'valid_token_value');
        $I->fillField('#authupdateform-code', '$objData->pets[0] = "dog"; $objData->name = "John"; $objData->age = 35;');
        $I->click('#auth-update-form button[type=submit]');
        $I->seeResponseCodeIs(200);
    }

    public function testUpdateAuthGet(AcceptanceTester $I)
    {
        $I->wantTo('Action (GET) with valid token');
        $I->selectOption('#authupdateform-method', 'GET');
        $I->fillField('#authupdateform-token', 'valid_token_value');
        $I->fillField('#authupdateform-code', '$objData->pets[0] = "dog"; $objData->name = "John"; $objData->age = 35;');
        $I->click('#auth-update-form button[type=submit]');
        $I->seeResponseCodeIs(200);
    }

    public function testUpdateAuthPostInvalidToken(AcceptanceTester $I)
    {
        $I->wantTo('Action (POST) with invalid token');
        $I->selectOption('#authupdateform-method', 'POST');
        $I->fillField('#authupdateform-token', 'invalid_token_value');
        $I->fillField('#authupdateform-code', '$objData->pets[0] = "dog"; $objData->name = "John"; $objData->age = 35;');
        $I->click('#auth-update-form button[type=submit]');
        $I->seeResponseCodeIs(400);
    }

    public function testUpdateAuthGetInvalidToken(AcceptanceTester $I)
    {
        $I->wantTo('Action (GET) with invalid token');
        $I->selectOption('#authupdateform-method', 'GET');
        $I->fillField('#authupdateform-token', 'invalid_token_value');
        $I->fillField('#authupdateform-code', '$objData->pets[0] = "dog"; $objData->name = "John"; $objData->age = 35;');
        $I->click('#auth-update-form button[type=submit]');
        $I->seeResponseCodeIs(400);
    }
}