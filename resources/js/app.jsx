import "./bootstrap";
import React from "react";
import { createRoot } from "react-dom/client";
import PagesFlow from "./pages/PagesFlow";
import "reactflow/dist/style.css";

// const mountedRoots = new WeakMap();

// function mountPagesFlow() {
//     document.querySelectorAll("[data-react-pages-flow]").forEach((el) => {
//         const rect = el.getBoundingClientRect();

//         // HARD guard: wait until layout is stable
//         if (rect.width === 0 || rect.height === 0) {
//             setTimeout(mountPagesFlow, 50);
//             return;
//         }

//         const pages = JSON.parse(el.dataset.pages || "[]");
//         const version = el.dataset.version;
//         console.log("Ver: ", version);

//         let root = mountedRoots.get(el);

//         if (!root) {
//             root = createRoot(el);
//             mountedRoots.set(el, root);
//         }

//         root.render(<PagesFlow key={version} pages={pages} />);
//     });
// }

// // Initial page load
// document.addEventListener("DOMContentLoaded", mountPagesFlow);

// // After wire:navigate finishes AND DOM is painted
// document.addEventListener("livewire:navigated", () => {
//     setTimeout(mountPagesFlow, 0);
// });

// // After perPage / search updates
// document.addEventListener("livewire:message.processed", () => {
//     setTimeout(mountPagesFlow, 0);
// });

const mountedRoots = new WeakMap();

function mountPagesFlow(pages) {
    const el = document.getElementById("pages-flow-root");
    if (!el) return;

    // Ensure container has width/height
    const mountReactFlow = () => {
        if (el.offsetWidth === 0 || el.offsetHeight === 0) {
            requestAnimationFrame(mountReactFlow);
            return;
        }

        let root = mountedRoots.get(el);
        if (!root) {
            root = createRoot(el);
            mountedRoots.set(el, root);
        }

        root.render(<PagesFlow pages={pages} />);
    };

    mountReactFlow();
}

// Initial mount (if pages are passed via Blade)
document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("pages-flow-root");
    if (!el) return;

    // Optional: initial pages from Blade
    const initialPages = el.dataset.pages ? JSON.parse(el.dataset.pages) : [];
    if (initialPages.length) mountPagesFlow(initialPages);
});

// // After wire:navigate finishes AND DOM is painted
document.addEventListener("livewire:navigated", () => {
    const el = document.getElementById("pages-flow-root");
    if (!el) return;

    // Optional: initial pages from Blade
    const initialPages = el.dataset.pages ? JSON.parse(el.dataset.pages) : [];
    if (initialPages.length) mountPagesFlow(initialPages);
});

// React Flow update on Livewire events
document.addEventListener("pages-updated", (event) => {
    const pages = event.detail[0].pages;

    mountPagesFlow(pages);
});
