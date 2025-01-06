<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Enum;

enum Finality: string
{
    case PAGAMENTO_IMPOSTOS_E_TAXAS = 'pagamento_impostos_e_taxas';

    case PAGAMENTO_CONCESSIONARIAS = 'pagamento_concessionarias';

    case PAGAMENTO_DIVIDENDENDOS = 'pagamento_dividendos';

    case PAGAMENTO_SALARIOS = 'pagamento_salarios';

    case PAGAMENTO_FORNECEDORES = 'pagamento_fornecedores';

    case PAGAMENTO_HONORARIOS = 'pagamento_honorarios';

    case PAGAMENTO_ALUGUEIS_E_CONDOMINIOS = 'pagamento_alugueis_e_condominios';

    case PAGAMENTO_DUPLICATAS_E_TITULOS = 'pagamento_duplicatas_e_titulos';

    case PAGAMENTO_MENSALIDADE_ESCOLAR = 'pagamento_mensalidade_escolar';

    case CREDITO_CONTA = 'credito_conta';
}
