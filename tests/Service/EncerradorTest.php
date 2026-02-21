<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $leilao = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $leilao2 = new Leilao('Fusca 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = new LeilaoDao();
        $leilaoDao->salva($leilao);
        $leilaoDao->salva($leilao2);

        $encerrador = new Encerrador();
        $encerrador->encerra();

        $leiloes = $leilaoDao->recuperarFinalizados();

        self::assertCount(2, $leiloes);
        self::assertEquals('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());
        self::assertEquals('Fusca 1972 0KM', $leiloes[1]->recuperarDescricao());
    }
}