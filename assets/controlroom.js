/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// load stimulus controllers
import './stimulus_bootstrap.js';

// CSS can be imported here, or with @import from controlroom.css
// (AssetMapper rewrites CSS @import paths to their versioned URLs, as for app.css)
import './styles/controlroom.css';

import "./styles/component/modal-variables-controlroom.css";
import "./styles/component/modal.css";
import "./styles/component/modal-icon.css";
import "./styles/component/modal-animation.css";