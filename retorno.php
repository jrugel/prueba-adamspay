<?php
if(isset($_GET['type']) && isset($_GET['doc_id'])) {
    $fileName = implode("", [
        'respuestas/',
        $_GET['type'],
        '_',
        $_GET['doc_id'],
        '.json'
    ]);

    $respuesta = file_exists($fileName) ? json_decode(file_get_contents($fileName), true) : [];
} else {
    die("Uso indebido de esta página");
}

?>
<html>
    <head></head>
    <body>
        <?php if(count($respuesta) > 0) { ?>
            <h1>Cobro exitoso!</h1>
            <table border="1">
            <tr>
                    <th>Código de sesión</th>
                    <td><?=$respuesta['debt']['docId']?></td>
                </tr>
                <tr>
                    <th>Descripción</th>
                    <td><?=$respuesta['debt']['label']?></td>
                </tr>
                <tr>
                    <th>Fecha de compra</th>
                    <td><?=$respuesta['debt']['created']?></td>
                </tr>
                <tr>
                    <th>Monto</th>
                    <td><?=$respuesta['debt']['amount']['currency']?> <?=(int) $respuesta['debt']['amount']['paid']?></td>
                </tr>
                <tr>
                    <th>Detalle</th>
                    <td><table border="1">
                        <tr>
                            <th>ID Transacción</th>
                            <th>Medio de pago</th>
                            <th>Monto cobrado</th>
                        </tr>
                        <?php foreach($respuesta['debt']['refs']['txList'] as $detalle) { ?>
                            <tr>
                                <td><?=$detalle['txId']?></td>
                                <td><?=$detalle['method']?></td>
                                <td><?=$detalle['realAmount']['currency']?> <?=(int) $detalle['realAmount']['value']?></td>
                            </tr>
                        <?php } ?>
                    </table></td>
                </tr>
            </table>
        <?php } else { ?>
            <p>No se pudo realizar el cobro. Puede reintentar el pago en esta dirección: <a href="https://staging.adamspay.com/pay/el-mercadi369/debt/<?=$_GET['doc_id']?>">https://staging.adamspay.com/pay/el-mercadi369/debt/<?=$_GET['doc_id']?></a></p>
        <?php } ?>
    </body>
</html>