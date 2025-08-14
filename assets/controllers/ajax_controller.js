import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["form", "container", "spinner", "submitBtn"];

    async submit(event) {
        event.preventDefault();
        this.showLoading();

        try {
            const response = await fetch(this.formTarget.action, {
                method: "POST",
                body: new FormData(this.formTarget),
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, "text/html");
            const container = doc.querySelector("[data-ajax-target='container']");

            if (!container) throw new Error("No [data-ajax-target='container'] in response");

            this.containerTarget.replaceChildren(...container.childNodes);
            
        } catch (err) {
            console.error(err);
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        this.spinnerTarget.classList.remove("hidden");
        this.submitBtnTarget?.setAttribute("disabled", "disabled");
        this.submitBtnTarget?.setAttribute("aria-busy", "true");
    }

    hideLoading() {
        // guard in case the element was removed/replaced
        if (this.hasSpinnerTarget) this.spinnerTarget.classList.add("hidden");
        if (this.hasSubmitBtnTarget) {
            this.submitBtnTarget.removeAttribute("disabled");
            this.submitBtnTarget.removeAttribute("aria-busy");
        }
    }
}
