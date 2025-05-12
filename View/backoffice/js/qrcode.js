// Initialisation du modal QR code
let qrModal;

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le modal une seule fois au chargement de la page
    qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    console.log('Modal initialisé');
});

async function showQRCode(id) {
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

        const response = await fetch(`index.php?qrcode_id=${id}`);
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error || 'Erreur lors de la génération du QR code');
        }

        qrContainer.innerHTML = `
            <div>
                <img src="${data.qr_url}" alt="QR Code" class="img-fluid mb-3" style="max-width: 300px;">
                <div class="qr-details text-start">
                    <h6>Détails de la réclamation:</h6>
                    <p><strong>ID:</strong> ${data.data.id_rec}</p>
                    <p><strong>Nom:</strong> ${data.data.nomprenom}</p>
                    <p><strong>Email:</strong> ${data.data.email}</p>
                    <p><strong>Film:</strong> ${data.data.nomfilm}</p>
                    <p><strong>Type:</strong> ${data.data.type_rec}</p>
                    <p><strong>Détail:</strong> ${data.data.detail}</p>
                    <p><strong>Réponse:</strong> ${data.data.reponse_rec}</p>
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