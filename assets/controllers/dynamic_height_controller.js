import { Controller } from "@hotwired/stimulus";

/**
 * This might be not necessary if we can use dvh to account for keyboard height
 * But could not get that to work.
 * So, since there probably are browser compatibility issues as well,
 * Handling it in js seems like the safe thing to do.
 */
export default class extends Controller {

    initialize() {
        this.isMobile = this.isMobileDevice();
    }

    connect() {

        this.boundAdjustHeight = this.adjustHeight.bind(this);

        // Use visualViewport API if available
        if (window.visualViewport) {
            window.visualViewport.addEventListener("resize", this.boundAdjustHeight);
        } else {
            // Fallback to window resize event
            window.addEventListener("resize", this.boundAdjustHeight);
        }
    }

    disconnect() {
        if (window.visualViewport) {
            window.visualViewport.removeEventListener("resize", this.boundAdjustHeight);
        } else {
            window.removeEventListener("resize", this.boundAdjustHeight);
        }
    }

    adjustHeight() {
        const container = this.element;
        
        if (container && this.isMobile) {   
            const height = window.visualViewport?.height || window.innerHeight;

            container.style.height = `${height}px`;

            const chatController = this.application.getControllerForElementAndIdentifier(
                document.querySelector('#chat-box'), // The element where the emoji_picker_controller is attached
                "chat" // The identifier of the emoji_picker_controller
            );

            if (chatController) {
                chatController.scrollToBottom();
            }
        }
    }

    isMobileDevice() {
        const isTouchDevice = navigator.maxTouchPoints > 0;
        const isSmallScreen = window.innerWidth < 768; // Adjust breakpoint as needed
        const isUserAgentMobile = /Mobi|Android/i.test(navigator.userAgent);

        return isTouchDevice && (isSmallScreen || isUserAgentMobile);
    }
}