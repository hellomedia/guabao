import { Controller } from "@hotwired/stimulus"
import debounce from "lodash/debounce"

export default class extends Controller {
    static targets = ["map", "searchInput", "lat", "lng", "placeId", "name", "address"]

    connect() {
        if (!window.google || !window.google.maps) {
            window.initMap = () => this.initMap()
            return
        }

        this.initMap()
    }

    initMap() {
        const lat = parseFloat(this.latTarget.value.replace(',', '.') || 18.787252)
        const lng = parseFloat(this.lngTarget.value.replace(',', '.') || 98.987437)


        const center = { lat, lng }

        console.log(center);

        this.map = new google.maps.Map(this.mapTarget, {
            center,
            zoom: 17
        })

        this.marker = new google.maps.Marker({
            map: this.map,
            position: center,
            draggable: true
        })

        this.map.addListener("click", (e) => {
            this.marker.setPosition(e.latLng)
            this.updateLatLngInputs(e.latLng.lat(), e.latLng.lng())
            this.clearPlaceFields()
        })

        this.marker.addListener("dragend", (e) => {
            this.updateLatLngInputs(e.latLng.lat(), e.latLng.lng())
            this.clearPlaceFields()
        })

        this.attachDebouncedAutocomplete()
    }

    attachDebouncedAutocomplete() {
        const setupAutocomplete = () => {
            const autocomplete = new google.maps.places.Autocomplete(this.searchInputTarget)
            autocomplete.bindTo("bounds", this.map)

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace()
                if (!place.geometry) return

                this.map.panTo(place.geometry.location)
                this.marker.setPosition(place.geometry.location)
                this.updateLatLngInputs(
                    place.geometry.location.lat(),
                    place.geometry.location.lng()
                )

                this.placeIdTarget.value = place.place_id || ""
                this.nameTarget.value = place.name || ""
                this.addressTarget.value = place.formatted_address || ""
            })
        }

        // Debounced wrapper using lodash
        const debouncedInit = debounce(setupAutocomplete, 1000)

        this.searchInputTarget.addEventListener("input", () => {
            debouncedInit()
        })
    }

    updateLatLngInputs(lat, lng) {
        this.latTarget.value = lat.toFixed(7)
        this.lngTarget.value = lng.toFixed(7)
    }

    clearPlaceFields() {
        this.placeIdTarget.value = ""
        this.nameTarget.value = ""
        this.addressTarget.value = ""
    }
}