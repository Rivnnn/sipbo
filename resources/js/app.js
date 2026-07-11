import "./bootstrap";
import Alpine from "alpinejs";
import { createIcons, icons } from "lucide";

// ─── Alpine ───────────────────────────────────────
window.Alpine = Alpine;
Alpine.start();

// ─── Lucide ───────────────────────────────────────
const initIcons = (scope) =>
    createIcons({
        icons,
        nameAttr: "data-lucide",
        nodes: scope ? [scope] : undefined,
    });

document.addEventListener("DOMContentLoaded", () => initIcons());
document.addEventListener("alpine:initialized", () => initIcons());
document.addEventListener("alpine:navigated", () => initIcons());

// ─── Modal helpers ────────────────────────────────
window.openModal = (id) => {
    window.dispatchEvent(new CustomEvent(`open-modal-${id}`));
    // Re-init lucide hanya di dalam modal yang dibuka, bukan seluruh dokumen
    requestAnimationFrame(() => {
        const el = document.querySelector(`[data-modal-id="${id}"]`);
        if (el) initIcons(el);
    });
};

window.closeModal = (id) =>
    window.dispatchEvent(new CustomEvent(`close-modal-${id}`));

// ─── Global confirm modal (pengganti browser confirm()) ───
// Satu modal dipakai ulang di semua halaman lewat <x-confirm-modal> di
// layout utama, supaya tidak perlu duplikat markup modal di tiap baris
// tabel / tiap tombol (lebih ringan & konsisten tampilannya).
window.confirmAction = ({
    message = "Apakah Anda yakin?",
    formId = null,
    danger = false,
    confirmLabel = "Ya, Lanjutkan",
} = {}) => {
    window.__confirmFormId = formId;

    const msgEl = document.getElementById("confirm-modal-message");
    if (msgEl) msgEl.textContent = message;

    const btn = document.getElementById("confirm-modal-confirm-btn");
    if (btn) {
        btn.textContent = confirmLabel;
        btn.classList.remove(
            "bg-red-600",
            "hover:bg-red-700",
            "text-white",
            "bg-sipbo-gold",
            "hover:bg-sipbo-gold-light",
            "text-sipbo-bg",
        );
        if (danger) {
            btn.classList.add("bg-red-600", "hover:bg-red-700", "text-white");
        } else {
            btn.classList.add(
                "bg-sipbo-gold",
                "hover:bg-sipbo-gold-light",
                "text-sipbo-bg",
            );
        }
    }

    openModal("confirm-global");
};

window.__submitConfirmedAction = () => {
    closeModal("confirm-global");
    const formId = window.__confirmFormId;
    if (formId) {
        const form = document.getElementById(formId);
        if (form) form.submit();
    }
    window.__confirmFormId = null;
};
