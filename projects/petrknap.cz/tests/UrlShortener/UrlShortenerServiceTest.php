<?php

namespace PetrKnapCz\Test\UrlShortener;

use PetrKnapCz\Test\TestCase;
use PetrKnapCz\UrlShortener\Exception\Exception;
use PetrKnapCz\UrlShortener\Exception\RecordNotFoundException;
use PetrKnapCz\UrlShortener\UrlShortenerRecord;
use PetrKnapCz\UrlShortener\UrlShortenerService;

class UrlShortenerServiceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        /** @var \PDOStatement $statement */
        $statement = $this->get(\PDO::class)->prepare("-- noinspection SqlDialectInspection
INSERT INTO url_shortener__records (id, short, long, is_redirect) VALUES (?, ?, ?, ?)");

        $statement->execute([1, 'short', 'long', false]);
        $statement->execute([2, 'redirect_short', 'redirect_long', true]);
    }

    public function testIsRegistered()
    {
        $this->assertInstanceOf(
            UrlShortenerService::class,
            $this->get(UrlShortenerService::class)
        );
    }

    /**
     * @dataProvider dataGetRecord_returnsRecordWhenRecordExists
     * @param UrlShortenerRecord $expected
     * @param string $short
     */
    public function testGetRecord_returnsRecordWhenRecordExists(UrlShortenerRecord $expected, $short)
    {
        $this->assertEquals(
            $expected,
            $this->get(UrlShortenerService::class)->getRecord($short)
        );
    }

    public function dataGetRecord_returnsRecordWhenRecordExists()
    {
        return [
            [
                new UrlShortenerRecord(1, 'short', 'long', false),
                'short'
            ],
            [
                new UrlShortenerRecord(2, 'redirect_short', 'redirect_long', true),
                'redirect_short'
            ],
        ];
    }

    public function testGetRecord_throwsExceptionWhenRecordDoesNotExist()
    {
        $this->expectException(RecordNotFoundException::class);

        $this->get(UrlShortenerService::class)->getRecord('not_found');
    }
}