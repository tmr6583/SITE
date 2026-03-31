<?php
        $proibido = array('cc:', 'subject:', 'http:', 'https:', 'bcc:', '$_post', '$_get', '$_request', '$_globals', 'insert', 'delete', 'update', 'join', 'truncate', 'drop');
        $variaveis = $_REQUEST;

        foreach($variaveis as $valor) {
                foreach($proibido as $valor2) {
                        if (@strstr(strtolower($valor),$valor2)) {
                                die('informe somente query que retorne resultados.');
                        }
                }
        }
?>
