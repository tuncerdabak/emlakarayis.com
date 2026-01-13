const app = {
    // Initialize
    init() {
        // Form handling is now managed locally in pages for better control
        // this.setupForms(); 
    },

    // Handle initial verification request (send data -> get whatsapp link)
    async handleVerificationRequest(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        const formData = new FormData(e.target);
        const phone = formData.get('phone');

        // Save phone for next step
        document.getElementById('codePhone').value = phone; // formatPhone needed? backend handles it.

        try {
            btn.disabled = true;
            btn.innerHTML = 'İşleniyor...';

            const response = await fetch('api/dogrulama-talebi.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Open WhatsApp
                window.open(result.whatsappUrl, '_blank');

                // Show Step 2
                showStep('step-code');
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    },

    // Handle code verification (check code -> login)
    async handleCodeVerification(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        const formData = new FormData(e.target);

        try {
            btn.disabled = true;
            btn.innerHTML = 'Doğrulanıyor...';

            const response = await fetch('api/kod-dogrula.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = 'talep-gir.php';
            } else {
                alert(result.message);
                // clear inputs
                document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                document.querySelector('.otp-input').focus();
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
};

// Start App when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    app.init();
});
