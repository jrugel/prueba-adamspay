<?php
$post = file_get_contents('php://input'); // el POST
$secret = '109a766ef467e4df7d'; // Obtener del UI de administración
 
$hmac_esperado = md5( 'adams' . $post . $secret );
$hmac_recibido = @$_SERVER['HTTP_X_ADAMS_NOTIFY_HASH'];
 
if( $hmac_esperado == $hmac_recibido ){
    $respuestaLegible = json_decode($post, true);
    if(isset($respuestaLegible['debt']['docId'])) {
        $nombreDeArchivo = 'debt_' . $respuestaLegible['debt']['docId'];
    } else {
        $nombreDeArchivo = "algo_paso_aqui_" . uniqid(true);
    }

    $myfile = fopen("respuestas/${nombreDeArchivo}.json", "w") or die("Unable to open file!");
    fwrite($myfile, $post);
    fclose($myfile);    
} else {
    die('Validación ha fallado'); // Ignorar esta notificación
}
?>
