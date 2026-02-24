<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase 
{
    private \PDO $pdo;

    public function setUp(): void
    {
        $this->pdo = ConnectionCreator::getConnection();
        $this->pdo->beginTransaction();
    }

    public function testInsercaoEBuscaDevemFuncionar()
    {
        //arrange
        $leilao = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('2024-06-01 20:00:00'));
        $leilaoDao = new LeilaoDao($this->pdo);
        $leilaoDao->salva($leilao);
        
        //act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        //assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertEquals('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());

    }

    public function tearDown(): void
    {
        $this->pdo->rollBack();
    }
}