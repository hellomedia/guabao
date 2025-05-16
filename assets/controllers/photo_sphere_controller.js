import { Controller } from '@hotwired/stimulus';
import { Viewer } from '@photo-sphere-viewer/core';

export default class extends Controller {
    connect() {

        const viewer = new Viewer({
            container: this.element,
            panorama: this.element.dataset.src,
        });
    }
}