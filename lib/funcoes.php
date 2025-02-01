<?php
function formatPhoneNumber($phoneNumber) {
    // Define os DDDs que mantêm o nono dígito
    $dddsWithNineDigits = [
        "11", "12", "13", "14", "15", "16", "17", "18", "19",
        "21", "22", "24", "27", "28"
    ];
    
    // Extrai o DDD (os três primeiros dígitos após o código do país)
    $ddd = substr($phoneNumber, 3, 2);
    
    // Extrai o restante do número sem o código de país (+55)
    $number = substr($phoneNumber, 5);
    
    // Verifica se o DDD está na lista que mantém o nono dígito
    if (in_array($ddd, $dddsWithNineDigits)) {
        // Retorna o número sem modificações (mantém o nono dígito)
        return "+55" . $ddd . $number;
    } else {
        // Remove o nono dígito (primeiro dígito do número de celular)
        return "+55" . $ddd . substr($number, 1);
    }
}

?>