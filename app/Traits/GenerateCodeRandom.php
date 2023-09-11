<?php

namespace App\Traits;

trait GenerateCodeRandom
{
    public function generateCode(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $code .= $characters[$randomIndex];
        }
        return $code;
    }
}
