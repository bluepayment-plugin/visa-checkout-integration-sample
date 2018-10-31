/**
 * @author Blue Media S.A.
 * @copyright Blue Media S.A.
 * @version 1.0.0
 */

/**
 * @constructor
 * @param {string} apiKey
 */
function VisaCheckout(apiKey) {
    this.apiKey = apiKey;

    this.transactionAmount = 0;
    this.transactionCurrency = '';
}

VisaCheckout.prototype.init = function (onPaidCallback) {
    V.init({
        apikey: this.apiKey,
        paymentRequest: {
            currencyCode: this.transactionCurrency,
            subtotal: this.transactionAmount
        }
    });
    V.on('payment.success', onPaidCallback);
    V.on('payment.cancel', this.onCancelCallback);
    V.on('payment.error', this.onErrorCallback);
};

/**
 * Wyświetlenie komunikatu w przypadku anulowania transakcji.
 *
 * @param {object} data
 */
VisaCheckout.prototype.onCancelCallback = function (data) {
    console.warn(JSON.stringify(data));
};

/**
 * Wyświetlenie komunikatu w przypadku błędu.
 *
 * @param {object} data
 */
VisaCheckout.prototype.onErrorCallback = function (data) {
    console.error(JSON.stringify(data));
};

/**
 * Ustawia kwotę transakcji.
 *
 * @param {numeric} amount
 */
VisaCheckout.prototype.setTransactionAmount = function (amount) {
    this.transactionAmount = amount;
};

/**
 * Ustawia walutę transakcji.
 *
 * @param {string} currency
 */
VisaCheckout.prototype.setTransactionCurrency = function (currency) {
    this.transactionCurrency = currency;
};