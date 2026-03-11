<?php
function validar_mfa_php($secreto, $codigo) {
    // Escapamos los argumentos por seguridad
    $secreto_esc = escapeshellarg($secreto);
    $codigo_esc = escapeshellarg($codigo);
    
    // Llamamos al script de Python de la Fase 6
    $comando = "python3 /scripts/mfa_validator.py $secreto_esc $codigo_esc";
    exec($comando, $output, $return_var);
    
    return ($return_var === 0);
}
?>
