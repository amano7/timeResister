<?php

namespace Amano7\RedmineTimeRegister;

class LineParser
{
    /**
     * @param string $line
     *
     * @return  array [
     *              'redNum' => <<TicketNumber>>,
     *              'redCom' => <<Comments>>,
     *              'redTime' => <<Hours>>,
     *          ]
     */
    public function parse(string $line): array
    {
        return [
            'redNum' => 1047,
            'redCom' => '事前検証テストケース一覧の更新',
            'redTime' => 1.00,
        ];
    }
}
