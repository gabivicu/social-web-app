import './styles/app.css';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.min.css';

// Dark mode toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('dark-mode-toggle');
    if (toggle) {
        const html = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }

        toggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        });
    }

    // Avatar crop functionality (only on settings page)
    initAvatarCrop();
});

function initAvatarCrop() {
    const cropModal = document.getElementById('crop-modal');
    const cropImage = document.getElementById('crop-image');
    const cropSaveBtn = document.getElementById('crop-save');
    const cropCancelBtn = document.getElementById('crop-cancel');

    // Find the file input for image upload
    const fileInput = document.querySelector('input[type="file"][id$="imageFile"]');

    if (!cropModal || !cropImage || !fileInput) return;

    let cropper = null;

    // When user selects a file, open crop modal
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
            cropImage.src = ev.target.result;
            cropModal.classList.remove('hidden');

            // Destroy previous cropper instance if any
            if (cropper) {
                cropper.destroy();
            }

            // Wait for image to load before initializing Cropper
            cropImage.onload = () => {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                    guides: true,
                    center: true,
                    highlight: false,
                    background: true,
                    responsive: true,
                });
            };
        };
        reader.readAsDataURL(file);
    });

    // Crop & Save button
    cropSaveBtn.addEventListener('click', () => {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob((blob) => {
            // Create a new File from the cropped blob
            const croppedFile = new File([blob], 'avatar.png', { type: 'image/png' });

            // Replace the file input's files using DataTransfer
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            fileInput.files = dataTransfer.files;

            // Update avatar preview
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarFallback = document.getElementById('avatar-preview-fallback');

            if (avatarPreview) {
                avatarPreview.src = canvas.toDataURL('image/png');
            } else if (avatarFallback) {
                // Replace the fallback div with an img element
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = canvas.toDataURL('image/png');
                img.alt = 'Profile image';
                img.className = 'w-24 h-24 rounded-full object-cover ring-4 ring-indigo-100 dark:ring-gray-700 shadow-lg cursor-pointer';
                avatarFallback.replaceWith(img);
            }

            // Close modal and cleanup
            closeModal();
        }, 'image/png');
    });

    // Cancel button
    cropCancelBtn.addEventListener('click', () => {
        // Clear the file input so no file is submitted
        fileInput.value = '';
        closeModal();
    });

    // Close on backdrop click
    cropModal.addEventListener('click', (e) => {
        if (e.target === cropModal) {
            fileInput.value = '';
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !cropModal.classList.contains('hidden')) {
            fileInput.value = '';
            closeModal();
        }
    });

    function closeModal() {
        cropModal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }
}
