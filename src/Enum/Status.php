<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Enum;

enum Status: string
{
    case REGISTRO = 'registro';

    case ALTERACAO = 'alteracao';

    case BAIXA = 'baixa';

    case ALTERACAO_DATA = 'alteracao_data';
}
