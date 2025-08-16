import { Controller } from "@hotwired/stimulus";

/*
USAGE
=====
form_theme.html.twig:
{% block textarea_widget %}
	{%- set attr = attr|merge({'data-controller': 'autogrow', 'data-action': 'input->autogrow#resize'}) -%}
	{{ parent() }}
{% endblock %}
*/
export default class extends Controller {

    connect() {
        this.resize();
    }

    resize() {
        this.element.style.height = "auto"; // Reset height to calculate the new size
        this.element.style.height = `${this.element.scrollHeight}px`; // Set height to the scroll height
    }

}