<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Receipt\Cnab240\Bank;

use EdineiValdameri\Payments\Enum\Bank;
use EdineiValdameri\Payments\Enum\Status;
use EdineiValdameri\Payments\Interface\Payer;
use EdineiValdameri\Payments\Receipt\Cnab240\AbstractReceipt;
use EdineiValdameri\Payments\Receipt\Cnab240\Detail;
use Exception;

class Bradesco extends AbstractReceipt
{
    protected string $endLine = "\r";

    private array $occurrences = [
        '00' => 'Crédito ou Débito Efetivado - Este código indica que o pagamento foi confirmado',
        '01' => 'Insuficiência de Fundos - Débito Não Efetuado',
        '02' => 'Crédito ou Débito Cancelado pelo Pagador/Credor',
        '03' => 'Débito Autorizado pela Agência – Efetuado',
        'AA' => 'Controle Inválido',
        'AB' => 'Tipo de Operação Inválido',
        'AC' => 'Tipo de Serviço Inválido',
        'AD' => 'Forma de Lançamento Inválida',
        'AE' => 'Tipo/Número de Inscrição Inválido',
        'AF' => 'Código de Convênio Inválido',
        'AG' => 'Agência/Conta Corrente/DV Inválido',
        'AH' => 'Nº Sequencial do Registro no Lote Inválido',
        'AI' => 'Código de Segmento de Detalhe Inválido',
        'AJ' => 'Tipo de Movimento Inválido',
        'AK' => 'Código da Câmara de Compensação do Banco Favorecido/Depositário Inválido',
        'AL' => 'Código do Banco Favorecido Inoperante nesta data ou Depositário Inválido',
        'AM' => 'Agência Mantenedora da Conta Corrente do Favorecido Inválida',
        'AN' => 'Conta Corrente/DV do Favorecido Inválido',
        'AO' => 'Nome do Favorecido Não Informado',
        'AP' => 'Data Lançamento Inválido',
        'AQ' => 'Tipo/Quantidade da Moeda Inválido',
        'AR' => 'Valor do Lançamento Inválido',
        'AT' => 'Tipo/Número de Inscrição do Favorecido Inválido',
        'AU' => 'Logradouro do Favorecido Não Informado',
        'AV' => 'Nº do Local do Favorecido Não Informado',
        'AW' => 'Cidade do Favorecido Não Informada',
        'AX' => 'CEP/Complemento do Favorecido Inválido',
        'AY' => 'Sigla do Estado do Favorecido Inválida',
        'AZ' => 'Código/Nome do Banco Depositário Inválido',
        'BA' => 'Código/Nome da Agência Depositária Não Informado',
        'BB' => 'Seu Número Inválido',
        'BC' => 'Nosso Número Inválido',
        'BD' => 'Inclusão Efetuada com Sucesso',
        'BE' => 'Alteração Efetuada com Sucesso',
        'BF' => 'Exclusão Efetuada com Sucesso',
        'BG' => 'Agência/Conta Impedida Legalmente/Bloqueada.',
        'BH' => 'Empresa não pagou salário',
        'BI' => 'Falecimento do mutuário',
        'BJ' => 'Empresa não enviou remessa do mutuário',
        'BK' => 'Empresa não enviou remessa no vencimento',
        'BL' => 'Valor da parcela inválida',
        'BM' => 'Identificação do contrato inválida',
        'BN' => 'Operação de Consignação Incluída com Sucesso',
        'BO' => 'Operação de Consignação Alterada com Sucesso',
        'BP' => 'Operação de Consignação Excluída com Sucesso',
        'BQ' => 'Operação de Consignação Liquidada com Sucesso',
        'CA' => 'Código de Barras - Código do Banco Inválido',
        'CB' => 'Código de Barras - Código da Moeda Inválido',
        'CC' => 'Código de Barras - Dígito Verificador Geral Inválido',
        'CD' => 'Código de Barras - Valor do Título Divergente/Inválido.',
        'CE' => 'Código de Barras - Campo Livre Inválido',
        'CF' => 'Valor do Documento Inválido',
        'CG' => 'Valor do Abatimento Inválido',
        'CH' => 'Valor do Desconto Inválido',
        'CJ' => 'Valor da Multa Inválido',
        'CK' => 'Valor do IR Inválido',
        'CL' => 'Valor do ISS Inválido',
        'CM' => 'Valor do IOF Inválido',
        'CN' => 'Valor de Outras Deduções Inválido',
        'CO' => 'Valor de Outros Acréscimos Inválido',
        'CP' => 'Valor do INSS Inválido',
        'HA' => 'Lote Não Aceito',
        'HB' => 'Inscrição da Empresa Inválida para o Contrato',
        'HC' => 'Convênio com a Empresa Inexistente/Inválido para o Contrato',
        'HD' => 'Agência/Conta Corrente da Empresa Inexistente/Inválido para o Contrato',
        'HE' => 'Tipo de Serviço Inválido para o Contrato',
        'HF' => 'Conta Corrente da Empresa com Saldo Insuficiente',
        'HG' => 'Lote de Serviço Fora de Sequência',
        'HH' => 'Lote de Serviço InválidoVerificar se na data de transmissão existe arquivos com o mesmo',
        'HI' => 'Arquivo não aceito',
        'HJ' => 'Tipo de Registro Inválido',
        'HK' => 'Código Remessa/Retorno Inválido',
        'HL' => 'Versão de layout inválida',
        'HM' => 'Mutuário não identificado',
        'HN' => 'Tipo do benefício não permite empréstimo',
        'HO' => 'Benefício cessado/suspenso',
        'HP' => 'Benefício possui representante legal',
        'HQ' => 'Benefício é do tipo PA (Pensão alimentícia)',
        'HR' => 'Quantidade de contratos permitida excedida',
        'HS' => 'Benefício não pertence ao Banco informado',
        'HT' => 'Início do desconto informado já ultrapassado',
        'HU' => 'Número da parcela inválida',
        'HV' => 'Quantidade de parcela inválida',
        'HW' => 'Margem consignável excedida para o mutuário dentro do prazo do contrato',
        'HX' => 'Empréstimo já cadastrado',
        'HY' => 'Empréstimo inexistente',
        'HZ' => 'Empréstimo já encerrado',
        'H1' => 'Arquivo sem trailer',
        'H2' => 'Mutuário sem crédito na competência',
        'H3' => 'Não descontado – outros motivos',
        'H4' => 'Retorno de Crédito não pago',
        'H5' => 'Cancelamento de empréstimo retroativo',
        'H6' => 'Outros Motivos de Glosa',
        'H7' => 'Margem consignável excedida para o mutuário acima do prazo do contrato',
        'H8' => 'Mutuário desligado do empregador',
        'H9' => 'Mutuário afastado por licença',
        'IA' => 'Primeiro nome do mutuário diferente do primeiro nome do movimento do censo ou diferente da base de Titular do Benefício',
        'PA' => 'Pix não efetivado - Tente mais tarde"',
        'PB' => 'Transação interrompida devido a erro no PSP do Recebedor”',
        'PC' => 'Número da conta transacional encerrada no PSP do Recebedor”',
        'PD' => 'Tipo incorreto para a conta transacional especificada”',
        'PE' => 'Tipo de transação não é suportado/autorizado na conta transacional especificada”',
        'PF' => 'CPF/CNPJ do usuário recebedor não é consistente com o titular da conta transacional especificada”',
        'PG' => 'CPF/CNPJ do usuário recebedor incorreto”',
        'PH' => 'Ordem rejeitada pelo PSP do Recebedor”',
        'PI' => 'ISPB do PSP do Pagador inválido ou inexistente”',
        'PJ' => 'Chave não cadastrada no DICT”',
        'PK' => 'QR COde Inválido/vencido”',
        'PL' => 'Forma de iniciação invalida',
        'PM' => 'Chave de Pagamento invalida',
        'PN' => 'Chave de Pagamento não informada',
        'TA' => 'Lote Não Aceito - Totais do Lote com Diferença',
        'YA' => 'Título Não Encontrado',
        'YB' => 'Identificador Registro Opcional Inválido',
        'YC' => 'Código Padrão Inválido',
        'YD' => 'Código de Ocorrência Inválido',
        'YE' => 'Complemento de Ocorrência InválidoOcorrência especifica para o tipo de serviço alegação de Pagador.',
        'YF' => 'Alegação já Informada',
        'ZA' => 'Agência/Conta do Favorecido Substituíd',
        'ZB' => 'Divergência entre o primeiro e último nome do beneficiário versus primeiro e último nome na Receita Federal',
        'ZC' => 'Confirmação de Antecipação de Valor',
        'ZD' => 'Antecipação Parcial de Valor',
        'ZE' => 'Título bloqueado na base',
        'ZF' => 'Sistema em contingência – título valor maior que referência',
        'ZG' => 'Sistema em contingência – título vencido',
        'ZH' => 'Sistema em contingência – título indexado',
        'ZI' => 'Beneficiário divergente - Dados do Beneficiário divergente do constante na CIP.',
        'ZJ' => 'Limite de pagamentos parciais excedidos',
        'ZK' => 'Boleto já liquidado - Título de cobrança já liquidado na base da CIP.',
        '5A' => 'Agendado sob lista de debito',
        '5B' => 'Pagamento não autoriza sob lista de debito',
        '5C' => 'Lista com mais de uma modalidade',
        '5D' => 'Lista com mais de uma data de pagamento',
        '5E' => 'Número de lista duplicado',
        '5F' => 'Lista de debito vencida e não autorizada',
        '5I' => 'Ordem de Pagamento emitida',
        '5J' => 'Ordem de pagamento com data limite vencida',
        '5M' => 'Número de lista de debito invalida',
        '5T' => 'Pagamento realizado em contrato na condição de TESTE',
    ];

    private array $statusByOccurrence = [
        'BD' => Status::LIQUIDADO,
        '00' => Status::LIQUIDADO,
        'CF' => Status::CANCELADO,
    ];

    public function __construct(
        Payer $payer,
        mixed $file,
    ) {
        parent::__construct($payer, $file);
        $this->bank = Bank::BRADESCO;
        $this->payments = collect();
    }

    public function processHeader(string $data): void
    {
    }

    public function processHeaderBatch(string $data): void
    {
    }

    public function processDetail(string $data): void
    {
        $segment = $this->getValue(14, 14, $data);

        match ($segment->getValue()) {
            'A' => $this->processDetailSegmentA($data),
            'B' => $this->processDetailSegmentB($data),
            default => throw new Exception('Invalid segment type'),
        };
    }

    public function processTrailerBatch(string $data): void
    {
    }

    public function processTrailer(string $data): void
    {
    }

    /** @infection-ignore-all */
    private function processDetailSegmentA(string $data): void
    {
        $key = $this->getValue(74, 93, $data);

        $detail = new Detail();
        $detail->setId((int) $key->getValue());

        $occurrence = $this->getValue(231, 240, $data);
        $detail->setOccurrence(trim($occurrence->getValue()));
        $detail->setMessage($this->occurrences[$detail->getOccurrence()] ?? 'Ocorrência não encontrada');
        $detail->setStatus($this->statusByOccurrence[$detail->getOccurrence()]);

        $this->addPayment($detail);
    }

    private function processDetailSegmentB(string $data): void
    {
    }
}
