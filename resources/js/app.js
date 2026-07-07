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
