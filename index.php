<?php

$apiUrl = 'https://staging.adamspay.com/api/v1/debts?update_if_exists=1';
$credenciales = [
    'apiKey' => 'adams-63c3a180c4839f',
    'apiSecret' => '109a766ef467e4df7d',
];

$comidas = ['Tarta', 'Sandwich', 'Jugo'];
$ingredientes = ['hongo', 'ciruela', 'pino', 'bambú', 'nuez', 'almendra', 'castaña', 'arroz', 'avena', 'cebada', 'trigo', 'verdura'];

$sesion = uniqid(true);
$factura = "001-002-" . $sesion;
$articulo = implode(" de ", [$comidas[array_rand($comidas)], $ingredientes[array_rand($ingredientes)]]);
$moneda = 'PYG';
$precio = random_int(1, 25) * 1000;




$ahora = new DateTimeImmutable('now',new DateTimeZone('UTC'));
$expira = $ahora->add(new DateInterval('P2D'));
 
$deuda = [
    'docId'=>$sesion,
    'label'=>'Factura de ventas: ' . $factura . " (${articulo})",
    'amount'=>['currency'=>$moneda,'value'=>$precio],
    'validPeriod'=>[
        'start'=>$ahora->format(DateTime::ATOM),
        'end'=>$expira->format(DateTime::ATOM)
    ]
];
 
$post = json_encode( ['debt'=>$deuda] );
 

// Para que no se imprima en la pantalla "Deuda creada exitosamente URL=..."
ob_start();


$curl = curl_init();
 
curl_setopt_array($curl,[
    CURLOPT_URL => $apiUrl,
    CURLOPT_HTTPHEADER => ['apikey: '.$credenciales['apiKey'],'Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST=>'POST',
    CURLOPT_POSTFIELDS=>$post
]);
 
$response = curl_exec($curl);
if( $response ){
  $data = json_decode($response,true);
 
  // Deuda es retornada en la propiedad "debt"
  $payUrl = isset($data['debt']) ? $data['debt']['payUrl'] : null;
  if( $payUrl ) {
    echo "Deuda creada exitosamente\n";
    echo "URL=$payUrl\n";
  } else {
    echo "No se pudo crear la deuda\n";
    print_r($data['meta']);
  }
 
}
else {
  echo 'curl_error: ',curl_error($curl);
}
curl_close($curl);


ob_end_clean();
?>

<html>
    <head>
    </head>

    <body>
        <h1>Prueba de implementación de AdamsPay</h1>
        <p>Para hacerla corta seguimos el Principio KISS, por lo tanto, vamos al grano:</p>

        <ol>
        <li>Creamos el carrito y le ponemos el ID de sesión <b><?=$sesion;?></b>
        <li>Decimos que vamos a vender una unidad del producto <b><?=$articulo?></b> que cuesta <b><?=$moneda?> <?=$precio?></b> y emitimos la factura <b><?=$factura;?></b></li>
        <li>Por el simple hecho de acelerar las cosas, al momento de cargar esta página ya hemos generado la deuda, usando el mismo identificador como ID de la Deuda</li>
        <?php if($payUrl) { ?>
        <li>El link de la deuda fue generado y se va a proceder a pagarlo al darle click aquí: <a href="<?=$payUrl?>"><?=$payUrl?></a></li>
        <?php } else { ?>
        <li>Algún problema se presentó, así que no se puede seguir.</li>
        <?php } ?>
        </ol>
    </body>
</html>
