<!DOCTYPE html>
<html>
    <body>

        <div style="margin: 20px auto">
            <h3>Hola {{ $giftcard->mailign_owner_name }}!</h3>
            <h5>Activa tus Gift Card x MESES siguiendo estos pasos:</h5>

            <ul>
                <li><b>Ingresa a:</b> craftimes.com/giftcard/canjea</li>
                <li><b>Ingresa este código:</b> {{ $giftcard->codigo }}</li>
            </ul>

            <p>
                <b>IMPORTANTE:</b> La entrega de tu PACK se hará  los primeros 10 días del mes siguiente,
                dentro de las 9 AM y  6 PM  en la dirección de entrega que has indicado.
            </p>

            <h3>SALUD !!!</h3>
        </div>

    </body>
</html>
