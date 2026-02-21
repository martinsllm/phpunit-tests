<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class LeilaoDaoMock extends LeilaoDao 
{
    private $leiloes = [];

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return $leilao->estaFinalizado();
        });
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return !$leilao->estaFinalizado();
        });
    }

    public function salva(Leilao $leilao): void
    {
        $this->leiloes[] = $leilao;
    }

    public function atualiza(Leilao $leilao)
    {
        // Não é necessário implementar nada aqui para o teste
    }

}
class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $leilao = new Leilao('Fiat 147 0KM', new \DateTimeImmutable('8 days ago'));
        $leilao2 = new Leilao('Fusca 1972 0KM', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = new LeilaoDaoMock();
        $leilaoDao->salva($leilao);
        $leilaoDao->salva($leilao2);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();
        
        $this->assertTrue($leilao->estaFinalizado());
        $this->assertTrue($leilao2->estaFinalizado());
    }
}