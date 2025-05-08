document.addEventListener('DOMContentLoaded', function () {
    qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    console.log('Modal initialisé');
});

async function showQRCodeForEvent(id) {
    if (!qrModal) {
        qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    }

    const qrContainer = document.getElementById('qrCodeContainer');
    const qrError = document.getElementById('qrError');

    try {
        qrModal.show();

        qrContainer.innerHTML = `
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;
        qrError.style.display = 'none';

        const response = await fetch(`index.php?qrcode_event_id=${id}`);
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success || !data.evenement) {
            throw new Error(data.error || 'Erreur lors de la génération du QR code pour l’événement');
        }

        const event = data.evenement;

        qrContainer.innerHTML = `
            <div>
                <img src="${data.qr_url}" alt="QR Code" class="img-fluid mb-3" style="max-width: 300px;">
                <div class="qr-details text-start">
                    <h6>Détails de l'événement:</h6>
                    <p><strong>ID:</strong> ${event.id_event}</p>
                    <p><strong>Nom:</strong> ${event.name_event}</p>
                    <p><strong>Lieu:</strong> ${event.location}</p>
                    <p><strong>Date:</strong> ${event.date_event}</p>
                    <p><strong>Places totales:</strong> ${event.total_places}</p>
                    <p><strong>Prix:</strong> ${event.price_event}</p>
                    <p><strong>Description:</strong> ${event.description}</p>
                    <p><strong>ID Film:</strong> ${event.id_film}</p>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Erreur:', error);
        qrError.textContent = `Erreur: ${error.message}`;
        qrError.style.display = 'block';
        qrContainer.innerHTML = '';
    }
}
