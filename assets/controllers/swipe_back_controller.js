import { Controller } from "@hotwired/stimulus";

export default class extends Controller {

    connect() {
        // swipe deltaX threshold
        // keep low to have the swipe take effect visually quickly -- since we don't drag
        // but not too low or else a vertical swipe to scroll might trigger the swipe back
        // if the swipe starts more horizontal than vertical
        this.threshold = 50;

        // Bind the methods and store references
        this.boundHandleTouchStart = this.handleTouchStart.bind(this);
        this.boundResetPosition = this.resetPosition.bind(this);

        // Add event listeners using the bound methods
        this.element.addEventListener("touchstart", this.boundHandleTouchStart, true);
        document.addEventListener("turbo:before-cache", this.boundResetPosition);
    }

    disconnect() {
        // Remove event listeners using the same bound methods
        this.element.removeEventListener("touchstart", this.boundHandleTouchStart, true);
        document.removeEventListener("turbo:before-cache", this.boundResetPosition);
    }

    handleTouchStart(event) {
        this.touchStartX = event.changedTouches[0].screenX;
        this.touchStartY = event.changedTouches[0].screenY;

        // only declare one animation frame request at a time
        this.animationFrameRequested = false;
        // At gesture end, ignore pending request animation frame
        this.ignorePendingRequestAnimationFrame = false;
        // reset in case of multiple drags/clicks
        this.deltaX = 0;
        this.deltaY = 0;

        this.boundHandleTouchMove = this.handleTouchMove.bind(this);
        this.boundHandleTouchEnd = this.handleTouchEnd.bind(this);

        this.element.addEventListener("touchmove", this.boundHandleTouchMove, true);
        this.element.addEventListener("touchend", this.boundHandleTouchEnd, true);
        this.element.addEventListener("touchcancel", this.boundHandleTouchEnd, true);
    }

    /**
     * For performance, keep this method as lean as possible 
     * (it is called on every touchmove event!)
     * and defer logic to onAnimationFrame(), 
     * which runs at intervals - and not on the main thread
     */
    handleTouchMove(event) {
        // only request one animation frame at a time
        if (!this.animationFrameRequested) {
            // arrow fonctions bind 'this' to the class automatically
            window.requestAnimationFrame(() => {
                this.onAnimationFrame(event);
                this.animationFrameRequested = false;
            });
            this.animationFrameRequested = true;
        }
    }

    onAnimationFrame(event) {
        if (this.ignorePendingRequestAnimationFrame) {
            return;
        }

        const currentX = event.changedTouches[0].screenX;
        const currentY = event.changedTouches[0].screenY;

        this.deltaX = currentX - this.touchStartX;
        this.deltaY = currentY - this.touchStartY;

        // DRAG -- DO NOT DRAG IN THIS CASE
        // If the threshold for deltaX triggering history.back() is low (like 20px),
        // transition is visually more coherent without dragging
        // CSS for drag:
        //     body, html { overflow-x: hidden; }
        //     #main-wrapper { position: relative; }
        // if (this.deltaX > 0) {
        //     document.getElementById('main-wrapper').style.transform = `translateX(-${this.deltaX}px)`;
        //     document.getElementById('main-wrapper').style.opacity = (300 - this.deltaX) / 300;
        // }

        // Instead, simply trigger history.back() quickly (after low threshold)
        // and let CSS handle the transition.
        // CSS for view transition (see view-transition.css)
        //    @keyframes move-out {
        //         from { transform: translateX(0%); }
        //         to { transform: translateX(-100%); }
        //         }
        //    @keyframes move-in {
        //         from { transform: translateX(100%); }
        //         to { transform: translateX(0%); }
        //     }
        //     /* Apply the custom animation to the old and new page states */
        //     ::view-transition-old(root) {
        //         animation: 0.4s ease-in both move-out;
        //     }
        //     ::view-transition-new (root) {
        //         animation: 0.4s ease-in both move-in;
        //     }


        if (this.deltaX > this.threshold && Math.abs(this.deltaX) > (Math.abs(this.deltaY) + 50)) {

            this.element.removeEventListener("touchmove", this.boundHandleTouchMove, true);
            this.element.removeEventListener("touchend", this.boundHandleTouchEnd, true);
            this.element.removeEventListener("touchcancel", this.boundHandleTouchEnd, true);

            history.back();
        }

    }

    handleTouchEnd(event) {
        // Ignore pending request animation frame logic
        this.ignorePendingRequestAnimationFrame = true;

        this.element.style.transition = "transform 0.3s ease";
        
        const touchEndX = event.changedTouches[0].screenX;
        const touchEndY = event.changedTouches[0].screenY;

        this.deltaX = touchEndX - this.touchStartX;
        this.deltaY = touchEndY - this.touchStartY;

        this.element.removeEventListener("touchmove", this.boundHandleTouchMove, true);
        this.element.removeEventListener("touchend", this.boundHandleTouchEnd, true);
        this.element.removeEventListener("touchcancel", this.boundHandleTouchEnd, true);

        if (this.touchEndX - this.touchStartX > this.threshold && Math.abs(this.deltaX) > (Math.abs(this.deltaY) + 50)) {
            history.back();
        } else {
            // snap element back
            document.getElementById('main-wrapper').style.transform = "translateX(0)";
            document.getElementById('main-wrapper').style.opacity = 1;
        }
    }

    resetPosition() {
        // Reset element back in place before page is cached
        document.getElementById('main-wrapper').style.transform = "translateX(0)";
        document.getElementById('main-wrapper').style.transition = ""; // Clear transition to avoid flickering
    }

}
