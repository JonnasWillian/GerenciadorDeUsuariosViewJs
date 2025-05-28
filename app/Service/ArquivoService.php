<?php

namespace App\Service;

class ArquivoService {
    public function salveFile($arquivo){
        $fileLocal = $arquivo->store('arquivos', 'public');

        return $fileLocal;
    }
}