<?php

declare(strict_types=1);

namespace EdineiValdameri\Payments\Helper;

use Exception;

final class Useful
{
    public static function onlyNumbers(string $string): string
    {
        /** @var string $return */
        $return = preg_replace('/[^[:digit:]]/', '', $string);

        return $return;
    }

    public static function upper(string $string): string
    {
        return strtr(mb_strtoupper($string), 'àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ', 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß');
    }

    public static function formatCnab(string $tipo, string $valor, int $tamanho, int $dec = 0, string $sFill = ''): string
    {
        if ($dec < 0) {
            throw new Exception('Decimais não pode ser menor que 0');
        }

        if (!in_array($tipo, ['9', 'N', '9L', 'NL', 'A', 'X'], true)) {
            throw new Exception('Tipo inválido');
        }

        $tipo = self::upper($tipo);
        /** @var string $normalize */
        $normalize = self::normalizeChars($valor);
        $valor = self::upper($normalize);
        $type = 's';
        $left = '';

        if (in_array($tipo, ['9', 'N', '9L', 'NL'], true)) {
            if (in_array($tipo, ['9L', 'NL'], true)) {
                $valor = self::onlyNumbers($valor);
            }
            $sFill = 0;

            if ($dec >= 1) {
                $valor = sprintf("%.{$dec}f", $valor);
            }

            if (str_contains($valor, '.')) {
                $valor = str_replace('.', '', $valor);
            }
        } elseif (in_array($tipo, ['A', 'X'], true)) {
            $left = '-';
        }

        return sprintf("%{$left}{$sFill}{$tamanho}{$type}", substr($valor, 0, $tamanho));
    }

    public static function modulo11(string $value, int $factor = 2, int $base = 9, int $x10 = 0, int|string $resto10 = '0'): int|string
    {
        $sum = 0;
        $resto = 10;
        for ($i = strlen($value); $i > 0; $i--) {
            $sum += $value[$i - 1] * $factor; // @phpstan-ignore-line
            if ($factor === $base) {
                $factor = 1;
            }
            $factor++;
        }

        if ($x10 == 0) {
            $sum *= 10;
            $digito = $sum % 11;
            if ($digito === $resto) {
                return $resto10;
            }

            return $digito;
        }

        return $sum % 11;
    }

    public static function normalizeChars(string $string): array|string|null
    {
        $normalizeChars = [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ä' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'Eth',
            'Ñ' => 'N', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Ŕ' => 'R',

            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ä' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'eth',
            'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ŕ' => 'r', 'ÿ' => 'y',

            'ß' => 'sz', 'þ' => 'thorn', 'º' => '', 'ª' => '', '°' => '',
        ];

        return preg_replace('/[^0-9a-zA-Z !+=*\-,.;:%@_]/', '', strtr($string, $normalizeChars));
    }
}
