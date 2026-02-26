<?php

namespace Alura\Leilao\Tests\Integration\Web;

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{

    public function testApiDeveRetornarLeiloesNaoFinalizados()
    {
        //arrange
        $url = 'http://localhost:8080/rest.php';

        //act
        $response = file_get_contents($url);
        $leiloes = json_decode($response, true);

        //assert
        self::assertStringContainsString('200 OK', $http_response_header[0]);
        self::assertCount(4, $leiloes);
        self::assertEquals('Leilão 1', $leiloes[0]['descricao']);
        self::assertFalse($leiloes[0]['estaFinalizado']);
    }
    

}