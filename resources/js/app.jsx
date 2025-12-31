import "./bootstrap";
import React from "react";
import { createRoot } from "react-dom/client";
import PagesFlow from "./pages/PagesFlow";
import "reactflow/dist/style.css";

const mountedRoots = new WeakMap();

function mountPagesFlow() {
    document.querySelectorAll("[data-react-pages-flow]").forEach((el) => {
        if (mountedRoots.has(el)) return;

        const pages = el.dataset.pages;
        // console.log(JSON.stringify(JSON.parse(pages).data));

        const root = createRoot(el);
        root.render(<PagesFlow pages={JSON.parse(pages).data} />);

        mountedRoots.set(el, root);
    });
}

// Initial page load
document.addEventListener("DOMContentLoaded", mountPagesFlow);

// Livewire navigation (THIS IS THE KEY PART)
document.addEventListener("livewire:navigated", mountPagesFlow);
