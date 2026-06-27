import "./bootstrap";
import { createIcons, icons } from "lucide";

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("theme-toggle");
    if (!btn) return;

    btn.addEventListener("click", () => {
        const isDark = document.documentElement.classList.toggle("dark");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        lucide.createIcons();
    });
});

createIcons({ icons });
