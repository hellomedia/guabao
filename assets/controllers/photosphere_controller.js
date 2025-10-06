import { Controller } from '@hotwired/stimulus'
import { Viewer } from '@photo-sphere-viewer/core';

import '../vendor/@photo-sphere-viewer/core/index.min.css';
import '../styles/component/photosphere.css';

/* USAGE

    <a 
        href="{{ uploaded_asset(pano360) }}"
        data-controller="photosphere"
        data-action="click->photosphere#open"
        data-photosphere-src-value="{{ uploaded_asset(pano360) }}"
    >
        <twig:ux:icon name="sphere" />
        <img 
            src="{{ uploaded_asset(pano360)|imagine_filter('thumb_square_432') }}"
            class="rounded shadow-sm"
            width="700"
            height="700"
        />
    </a>

*/

export default class extends Controller {
    static values = {
        src: String,          // required: panorama URL
    };

    connect() {
        this.isOpen = false;
        this._onKeydown = (e) => (e.key === "Escape") && this.close();
    }

    async open(event) {
        if (event) event.preventDefault();
        if (this.isOpen) return;
        if (!this.hasSrcValue) return;

        this.isOpen = true;

        // Build overlay shell
        this._buildOverlay();

        // Create viewer
        this.viewer = new Viewer({
            container: this.overlayContent,
            panorama: this.srcValue,
            defaultZoomLvl: 0,
            navbar: [
                "zoom",
                "fullscreen",
            ],
        });

        document.addEventListener("keydown", this._onKeydown);
        document.body.style.overflow = "hidden"; // prevent background scroll
        requestAnimationFrame(() => this.overlay.classList.add("photosphere-open"));
    }

    close() {
        if (!this.isOpen) return;
        this.viewer?.destroy();
        this.viewer = null;

        document.removeEventListener("keydown", this._onKeydown);
        document.body.style.overflow = "";
        const el = this.overlay;
        this.overlay = null;
        this.overlayContent = null;

        if (el) {
            el.classList.remove("pano-open");
            // remove on transition end or after a fallback timeout
            const cleanup = () => { el.removeEventListener("transitionend", cleanup); el.remove(); this.isOpen = false; };
            el.addEventListener("transitionend", cleanup);
            setTimeout(cleanup, 200); // fallback
        } else {
            this.isOpen = false;
        }
    }

    // ---------- internals ----------

    _buildOverlay() {
        if (this.overlay) return;

        const overlay = document.createElement("div");
        overlay.className = "photosphere-overlay";
        overlay.innerHTML = `
      <div class="photosphere-backdrop" data-role="backdrop"></div>
      <div class="photosphere-dialog" role="dialog" aria-modal="true" aria-label="${this.captionValue || "360 viewer"}">
        <button type="button" class="photosphere-close" aria-label="Close">&times;</button>
        <div class="photosphere-content"></div>
        ${this.captionValue ? `<div class="photosphere-caption">${this.captionValue}</div>` : ""}
      </div>
    `;

        overlay.querySelector('[data-role="backdrop"]').addEventListener("click", () => this.close());
        overlay.querySelector(".photosphere-close").addEventListener("click", () => this.close());

        document.body.appendChild(overlay);

        this.overlay = overlay;
        this.overlayContent = overlay.querySelector(".photosphere-content");
    }
}

