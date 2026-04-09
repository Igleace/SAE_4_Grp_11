function initPaymentToggle(selectId, cardContainerId, paypalContainerId) {
    const modePaiement = document.getElementById(selectId);
    const carteCredit = document.getElementById(cardContainerId);
    const paypal = document.getElementById(paypalContainerId);

    if (!modePaiement || !carteCredit || !paypal) {
        return;
    }

    function updatePaymentDisplay() {
        if (modePaiement.value === 'carte_credit') {
            carteCredit.style.display = 'block';
            paypal.style.display = 'none';
        } else if (modePaiement.value === 'paypal') {
            carteCredit.style.display = 'none';
            paypal.style.display = 'block';
        }
    }

    modePaiement.addEventListener('change', updatePaymentDisplay);
    updatePaymentDisplay();
}

document.addEventListener('DOMContentLoaded', function () {
    initPaymentToggle('mode_paiement', 'carte_credit', 'paypal');
});