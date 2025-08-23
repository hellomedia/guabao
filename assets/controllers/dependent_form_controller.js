import { Controller } from "@hotwired/stimulus";

/**
 * Needs Turbo frame for frontend 
 * ( and dynamic forms on symfony backend )
 */
/*
USAGE
=====
<div
  data-controller="dependent-form"
  data-dependent-form-debounce-value="250"
>
  {{ form_start(form, {
    attr: { 'data-dependent-form-target': 'form', 'data-turbo-frame': 'dpdt_form' }
  }) }}

  <turbo-frame id="dpdt_form" data-action="turbo:frame-load->dependent-form#restoreFocus">
    {# Driver field(s) — changing these triggers a submit that refreshes only the frame #}
    {{ form_row(form.category, {
      attr: {
        'data-dependent-form-target': 'trigger',
        'data-action': 'change->dependent-form#onChange'
      }
    }) }}

    {# Dependent field(s) refreshed by server #}
    {{ form_row(form.subcategory) }}
    {{ form_row(form.otherField ?? null) }}
  </turbo-frame>

  {{ form_end(form) }}
</div>
*/
export default class extends Controller {
    static targets = ["form", "trigger"];
    static values = { debounce: { type: Number, default: 200 } };

    connect() {
        this._debouncedSubmit = this._debounce(() => {
            // remember last changed field to restore focus after swap
            if (document.activeElement?.name) this._lastFocusedName = document.activeElement.name;
            this.formTarget?.requestSubmit();
            console.log('request submit')
        }, this.debounceValue);
    }

    onChange() {
        console.log('on change');
        this._debouncedSubmit();
    }

    restoreFocus(event) {
        if (!this._lastFocusedName) return;
        const el = event.target.querySelector(`[name="${CSS.escape(this._lastFocusedName)}"]`);
        el?.focus?.();
    }

    _debounce(fn, wait) {
        let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
    }
}
