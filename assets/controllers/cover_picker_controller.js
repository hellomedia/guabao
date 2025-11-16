import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input", "item"];

    connect() {
        // Restore selected state when editing
        const current = this.inputTarget.value;
        if (current) {
            const selectedItem = this.itemTargets.find(el => el.dataset.id === current);
            if (selectedItem) {
                selectedItem.classList.add("selected");
            }
        }

        // Add click handlers (auto-applies when Turbo reloads)
        this.itemTargets.forEach(el => {
            el.addEventListener("click", () => this.select(el));
        });
    }

    select(el) {
        // ID from data-id
        const id = el.dataset.id;

        // Update hidden input used by the form
        this.inputTarget.value = id;

        // Remove selected from all
        this.itemTargets.forEach(item => item.classList.remove("selected"));

        // Add selected to clicked item
        el.classList.add("selected");
    }
}
