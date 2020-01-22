<?php
/**
 * @author Blue Media S.A.
 * @copyright Blue Media S.A.
 * @version 1.0.1
 */

/*
 * Konfiguracja parametrów Blue Media.
 * Jeżeli nie posiadasz jeszcze konta w Płatnościach online Blue Media wejdź na stronę https://platnosci.bm.pl
 * i postępuj zgodnie z instrukcjami.
 *
 * Uwaga! Poniższa konfiguracja dotyczy środowiska testowego, nie produkcyjnego!
 */

// Link do bramki, na który wysyłane są parametry do rozpoczęcia transakcji.
$bmGatewayUrl = 'https://pay-accept.bm.pl/payment';

// Identyfikator serwisu.
// Nadany przez Blue Media po założeniu konta w Płatnościach online.
$bmServiceId = 000000;

// Separator wartości parametrów używanych przy obliczaniu sumy kontrolnej.
// Dostępny po zalogowaniu się do panelu admina w szczegółach serwisu w sekcji Konfiguracja Hasha.
$bmServiceHashSeparator = '|';

// Klucz szyfrujący używany przy obliczaniu sumy kontrolnej.
// Dostępny po zalogowaniu się do panelu admina w szczegółach serwisu w sekcji Konfiguracja Hasha.
$bmServiceHashKey = 'd41d8cd98f00b204e9800998ecf8427e';

$bmTransactionAmount = '1.00';

/*
 * Konfiguracja Visa Checkout.
 * Jeżeli nie posiadasz jeszcze konta w Visa Developer Center wejdź na stronę https://developer.visa.com/portal/auth/register, aby się zarejestrować
 * i postępuj zgodnie z instrukcjami.
 *
 * Uwaga! Pamiętaj, aby dodać Blue Media jako partnera do swojego konta. W przeciwnym wypadku Blue Media nie będzie mogło obciążyć karty.
 * Dodawanie partnerów dostępne jest po zalogowaniu się na konto Visa Developer Center (https://developer.visa.com), wybraniu projektu i przejściu
 * do sekcji Relationships. Tam, w polu Partner Name należy podać Blue Media, natomiast w polu Relationship Name należy podać identyfikator
 * akceptanta nadany przez Blue Media.
 */

// Klucz API.
// Dostępny po zalogowaniu się na konto Visa Developer Center (https://developer.visa.com), wybraniu projektu i przejściu do sekcji Credentials.
$visaCheckoutApiKey = 'jaKnLcHsySrR7U4jG2jirZWlSWtGkHxL7whc8y8JXr1zS75xb';

$visaCheckoutJsUrl = 'https://sandbox-assets.secure.checkout.visa.com/checkout-widget/resources/js/integration/v1/sdk.js';

$visaCheckoutButtonImageUrl = 'https://sandbox.secure.checkout.visa.com/wallet-services-web/xo/button.png';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Przykład integracji z Visa Checkout</title>
</head>
<body>
<?php
if ('POST' === $_SERVER['REQUEST_METHOD']) {
    /*
     * Start transakcji metodą przedtransakcji.
     * Szczegółowe informacje na temat startowania transakcji metodą przedtransakcji znajdują się w dodatku do specyfikacji integracji.
     */
    $data = [
        'ServiceID'     => $bmServiceId,                          // Parametr wymagany.
        'OrderID'       => date('YmdHis'),                 // Parametr wymagany.
        'Amount'        => $bmTransactionAmount,                  // Parametr wymagany.
        'Description'   => 'Visa Checkout test',                  // Parametr wymagany.
        'GatewayID'     => '1511',
        'Currency'      => 'PLN',
        'CustomerEmail' => 'test@example.com',
        'CustomerIP'    => '127.0.0.1',
        'Title'         => 'Visa Checkout test',
        // Kodujemy dane uzyskane od Visa Base64.
        'PaymentToken'  => base64_encode($_POST['paymentToken']), // Parametr wymagany.
    ];

    // Obliczamy hash.
    $data['Hash'] = hash(
        'sha256',
        implode($bmServiceHashSeparator, array_values($data)) . $bmServiceHashSeparator . $bmServiceHashKey
    );

    $handle = curl_init();

    curl_setopt_array(
        $handle,
        [
            CURLOPT_HTTPHEADER     => [
                'BmHeader: pay-bm-continue-transaction-url',
            ],
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $bmGatewayUrl,
        ]
    );

    $response = curl_exec($handle);

    echo htmlentities($response);
} else {
    ?>
    <form id="form" method="post">
        <input id="js-payment-token" name="paymentToken" type="hidden" value="">
    </form>
    <div class="js-pay-button-wrapper">
        <img class="v-button" alt="Visa Checkout" src="<?php echo $visaCheckoutButtonImageUrl; ?>" role="button">
    </div>
    <script src="js/visa-checkout.js"></script>
    <script src="<?php echo $visaCheckoutJsUrl; ?>"></script>
    <script>
        const vc = new VisaCheckout('<?php echo $visaCheckoutApiKey ?>');
        vc.setTransactionAmount(<?php echo $bmTransactionAmount; ?>);
        vc.setTransactionCurrency('PLN');
        vc.init(function (paymentData) {
            document.getElementById('js-payment-token').value = paymentData.callid;
            document.getElementById('form').submit();
        })
    </script>
    <?php
}
?>
</body>
</html>