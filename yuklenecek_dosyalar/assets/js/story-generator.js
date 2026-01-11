/**
 * Story Generator for Emlak Arayış
 * Uses html2canvas to render the #storyCardTemplate into an image
 */

document.addEventListener('DOMContentLoaded', () => {
    const generateBtn = document.getElementById('generateStoryBtn');
    const modal = document.getElementById('storyPreviewModal');
    const closeModalBtn = document.getElementById('closeStoryModal');
    const previewImage = document.getElementById('generatedStoryImage');
    const downloadBtn = document.getElementById('downloadStoryBtn');
    const loadingState = document.getElementById('storyLoading');
    const previewContent = document.getElementById('storyPreviewContent');
    const storyCard = document.getElementById('storyCardTemplate');

    if (!generateBtn || !storyCard) return;

    generateBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        
        // Open Modal & Show Loading
        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        previewContent.classList.add('hidden');
        
        try {
            // Wait a moment for the hidden element to be rendered properly if needed
            // Even though it's off-screen, html2canvas needs it in the DOM
            
            const canvas = await html2canvas(storyCard, {
                scale: 2, // Retina quality
                logging: false,
                useCORS: true, // If we have external images
                backgroundColor: null,
                allowTaint: true
            });
            
            // Convert to simple image
            const info = canvas.toDataURL("image/jpeg", 0.9);
            
            // Display
            previewImage.src = info;
            downloadBtn.href = info;
            downloadBtn.download = 'emlak-arayis-story.jpg';
            
            // Show Content
            loadingState.classList.add('hidden');
            previewContent.classList.remove('hidden');
            
        } catch (err) {
            console.error('Görsel oluşturma hatası:', err);
            alert('Görsel oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
            modal.classList.add('hidden');
        }
    });
    
    // Close Modal
    const closeModal = () => {
        modal.classList.add('hidden');
    };
    
    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});
