<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase 
{
    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('CREATE TABLE leiloes (
            id INTEGER PRIMARY KEY,
            descricao TEXT,
            dataInicio TEXT,
            finalizado BOOLEAN
        )');
    }

    public function setUp(): void
    {
        
        self::$pdo->beginTransaction();
    }

    #[DataProvider('leiloes')]
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        //arrange
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }
        
        //act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        //assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertEquals('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());
        self::assertFalse($leiloes[0]->estaFinalizado());
    }

    #[DataProvider('leiloes')]
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        //arrange
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }
       
        //act
        $leiloes = $leilaoDao->recuperarFinalizados();

        //assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertEquals('Brasilia 0KM', $leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->estaFinalizado());
    }

    public function testAoAtualizarLeilaoStatusDeveSerAlterado()
    {
        //arrange
        $leilaoDao = new LeilaoDao(self::$pdo);
        $leilaoDao->salva(new Leilao('Fiat 147 0KM'));

        //act
        $leilao = $leilaoDao->recuperarNaoFinalizados()[0];
        $leilao->finaliza();
        $leilaoDao->atualiza($leilao);  

        //assert
        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertEquals('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->estaFinalizado());
    }   

    public function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    public static function leiloes()
    {
        $naoFinalizado = new Leilao('Fiat 147 0KM');
        $finalizado = new Leilao('Brasilia 0KM');
        $finalizado->finaliza();

        return [
            [
                [$naoFinalizado, $finalizado]
            ]
        ];
    }
}