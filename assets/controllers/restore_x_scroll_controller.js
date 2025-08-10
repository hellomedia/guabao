import { Controller } from "@hotwired/stimulus";

/**
 * Usage:
 * <div
 *   data-controller="restore-x-scroll"
 *   data-restore-x-scroll-key-value="foo"  <!-- same key on all pages in the section -->
 *   data-restore-x-scroll-behavior-value="auto"
 * >
 *   ... horizontally scrollable flex row ...
 * </div>
 */
export default class extends Controller {
    static values = {
        key: String,                     // required: shared key across pages in the section
        behavior: { type: String, default: "auto" }, // "auto" | "smooth"
    };

    connect() {
        this._store = sessionStorage;
        this._k = `xscroll:${this.keyValue}`;

        // Restore once layout is ready
        requestAnimationFrame(() => this._restore());

        // Save on every scroll
        this._onScroll = () => this._store.setItem(this._k, String(this.element.scrollLeft));
        this.element.addEventListener("scroll", this._onScroll, { passive: true });

        // Also restore on BFCache back/forward
        this._onPageShow = () => this._restore();
        window.addEventListener("pageshow", this._onPageShow);
    }

    disconnect() {
        this.element.removeEventListener("scroll", this._onScroll);
        window.removeEventListener("pageshow", this._onPageShow);
    }

    _restore() {
        const raw = this._store.getItem(this._k);
        if (raw == null) return;
        const max = this.element.scrollWidth - this.element.clientWidth;
        if (max <= 0) return;
        const target = Math.max(0, Math.min(parseInt(raw, 10) || 0, max));
        this.element.scrollTo({ left: target, behavior: this.behaviorValue });
    }
}
