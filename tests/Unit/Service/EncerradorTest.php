<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private $encerrador;
    private $enviadorEmail;
    private $leilao;
    private $leilao2;

    protected function setUp(): void
    {
        $this->leilao = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $this->leilao2 = new Leilao('Fusca 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->createMock(LeilaoDao::class);
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilao, $this->leilao2]);
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$this->leilao, $this->leilao2]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withParameterSetsInOrder([$this->leilao], [$this->leilao2]);

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao');

        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testVerificaSeLeilaoFoiEncerrado()
    {
        $this->encerrador->encerra();
        
        $this->assertTrue($this->leilao->estaFinalizado());
        $this->assertTrue($this->leilao2->estaFinalizado());
    }

    public function testDeveContinuarProcesamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \RuntimeException('Erro ao enviar email de notificação.');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->will($this->throwException($e));
        $this->encerrador->encerra();
    }

    public function testSoDeveEnviarEmailAposFinalizarLeilao()
    {
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                $this->assertTrue($leilao->estaFinalizado(), 'O leilão deve estar finalizado antes de enviar email.');
            });

        $this->encerrador->encerra();
    }
}