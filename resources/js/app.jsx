import "./bootstrap";
import React from "react";
import { createRoot } from "react-dom/client";
import PagesFlow from "./pages/PagesFlow";
import "reactflow/dist/style.css";

const mountedRoots = new WeakMap();

function mountPagesFlow(pages) {
    const el = document.getElementById("pages-flow-root");
    if (!el) return;

    let root = mountedRoots.get(el);
    if (!root) {
        root = createRoot(el);
        mountedRoots.set(el, root);
    }

    root.render(<PagesFlow pages={pages} />);
}

// Handle initial page load and wire:navigate events
["DOMContentLoaded", "livewire:navigated"].forEach((eventName) => {
    document.addEventListener(eventName, () => {
        const el = document.getElementById("pages-flow-root");
        if (!el) return;

        const initialPages = el.dataset.pages
            ? JSON.parse(el.dataset.pages)
            : [];
        if (initialPages.length) mountPagesFlow(initialPages);
    });
});

// React Flow update on Livewire events
document.addEventListener("pages-updated", (event) =>
    mountPagesFlow(event.detail[0].pages || [])
);
