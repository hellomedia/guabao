// assets/controllers/media_description_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["form", "container"];

    async submit(event) {
        event.preventDefault();

        const response = await fetch(this.formTarget.action, {
            method: "POST",
            body: new FormData(this.formTarget),
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (response.ok) {
            const html = await response.text();
            this.containerTarget.outerHTML = html;
        } else {
            console.error("Failed to update media description.");
        }
    }
}
