<?php

namespace Amano7\RedmineTimeRegister;

/**
 * 正規表現による編集業取得アルゴリズムのテスト
 */
class Regext
{
    /**
     * 正規表現による編集業取得アルゴリズム
     *
     * @param  string  $regSample
     *
     * @return  string
     */
    public function regtest(string $regSample): array {
        $pattern = '/^- ([0-9]{1,2}:[0-9]{2})-([0-9]{1,2}:[0-9]{2})( .+[^0-9]{1,2}[^:][^0-9]{2})$/u';
        preg_match($pattern, $regSample, $matchNumber);
        return [$regSample, $matchNumber[3]];
        if (isset($matchNumber)) {
        } else {
            return [$regSample, ''];
        }
    }
}