import { Controller } from '@hotwired/stimulus'
import { Fancybox } from "@fancyapps/ui";
import { Viewer } from '@photo-sphere-viewer/core';

import '../styles/vendor/fancybox.css';
import '../vendor/@photo-sphere-viewer/core/index.min.css';

export default class extends Controller {
    connect() {
        Fancybox.bind('[data-fancybox]', {
            animated: true,
            showClass: 'fancybox-fadeIn',
            hideClass: 'fancybox-fadeOut',

            on: {
                reveal: (fancybox, slide) => {
                    const viewerContainer = slide.contentEl?.querySelector('.panorama-viewer')
                    const src = viewerContainer?.dataset.src

                    console.log('loaded');
                    console.log(slide)
                    console.log(slide.contentEl);
                    console.log(src);

                    if (viewerContainer && src && !viewerContainer.dataset.initiated) {
                        new Viewer({
                            container: viewerContainer,
                            panorama: src,
                            navbar: true,
                            defaultLat: 0,
                            defaultLong: 0,
                        })

                        viewerContainer.dataset.initiated = 'true'
                    }
                }
            }
        })
    }
}
