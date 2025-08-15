import { Controller } from "@hotwired/stimulus"

/** 
 * Infinite scroll WITOUT TURBO 
 */
export default class extends Controller {

    static targets = ["nextPage"];

    static values = {
        nextPageUrl: String 
    }

    connect() {
        // we can already be at the end of scroll if:
        //   - only 1 page
        //   - come from back button with scroll position at the "last page" part of loaded items
        this.endInfiniteScroll = !this.hasNextPageUrlValue || this.nextPageUrlValue == '';

        this.scrollTimer = null
        this.loading = false;

        window.addEventListener('scroll', this.onScroll)

        this.onScroll(); // initial check for loading next content
    }

    disconnect() {
        window.removeEventListener('scroll', this.onScroll);

        if (this.scrollTimer !== null) {
            clearTimeout(this.scrollTimer);
            this.scrollTimer = null;
        }
    }

    onScroll = () => {

        if (this.scrollTimer !== null || this.loading || this.endInfiniteScroll) return

        // Added a debounce / timer in addition to the loading flag
        // Loading flag by itself wasn't preventing some duplicate calls to loadMore()
        this.scrollTimer = setTimeout(() => {
            const thresholdPx = 400; // how early to prefetch
            const rect = this.nextPageTarget.getBoundingClientRect();

            if (rect.top - window.innerHeight <= thresholdPx) {
                this.loadMore();
            }

            this.scrollTimer = null; // release debounce
        }, 150) // ms delay between triggers
    }

    async loadMore() {
        
        this.loading = true
        
        const url = new URL(this.nextPageUrlValue, window.location.origin);

        try {
            const response = await fetch(url.toString())

            const html = await response.text()
            
            // TODO
            const doc = new DOMParser().parseFromString(html, "text/html");
            const newItems = doc.querySelector(".current-page");
            if (!newItems) return;

            this.nextPageTarget.insertAdjacentHTML("beforebegin", newItems.innerHTML);
  
            // Detect end-of-list from the marker in the fetched page
            const atEnd = !!doc.querySelector("[data-infinite-scroll-end]");
            if (atEnd) {
                this.endInfiniteScroll = true;
                this.nextPageUrlValue = ""; // stop
            } else {
                this._updateNextPageUrlValue(url); // keep going
            }

        } catch (error) {
            console.error('Infinite scroll failed:', error)
        } finally {
            this.loading = false
        }
    }

    _updateNextPageUrlValue(url) {
        const currentPath = url.pathname; // e.g. '/foo/bar' or '/foo/bar/2'

        // Split the path into segments:
        const segments = currentPath.split('/').filter(Boolean); // removes empty segments

        // Check if the last segment is a number (page):
        const lastSegment = segments[segments.length - 1];
        const pageNumber = parseInt(lastSegment, 10);

        if (!isNaN(pageNumber)) {
            // Last segment is a page number — increment it:
            segments[segments.length - 1] = (pageNumber + 1).toString();
        } else {
            // No page number segment yet — assume we want to go to page 2:
            segments.push('2');
        }

        // Rebuild the path:
        url.pathname = '/' + segments.join('/');

        // Preserve query parameters:
        this.nextPageUrlValue = url.toString();
    }
}
