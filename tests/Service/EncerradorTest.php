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

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$leilao, $leilao2]);
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$leilao, $leilao2]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withParameterSetsInOrder([$leilao], [$leilao2]);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();
        
        $this->assertTrue($leilao->estaFinalizado());
        $this->assertTrue($leilao2->estaFinalizado());
    }
}