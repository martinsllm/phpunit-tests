<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{
    public function notificarTerminoLeilao(Leilao $leilao)
    {
        $emailEnviado = mail(
            'usuario@email.com', 
            'Leilão encerrado', 
            'O leilão do item ' . $leilao->recuperarDescricao() . ' foi encerrado.'
        );

        if(!$emailEnviado) {
            throw new \RuntimeException('Erro ao enviar email de notificação.');
        }
    }
}