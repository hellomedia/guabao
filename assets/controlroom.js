/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// load stimulus controllers
import './bootstrap.js';

// Load all CSS directly in this file
// DO NOT USE 2nd level imports ( like... import '.styles/controlroom.css -- and then import css from there )
// 2nd level imports are not handled by asset mapper
// They can be handled by tailwind -- as we do for assets/styles/app.css
// BUT symfonycast/tailwind binary which builds tailwind assets only supports 1 entry point
// ( see symfonycasts_tailwind.yaml )
// So in the end, 2nd level imports are not recognized in a file other than app.css
import './styles/controlroom.css';

import "./styles/components/modal-variables-controlroom.css";
import "./styles/components/modal.css";
import "./styles/components/modal-icon.css";
import "./styles/components/modal-animation.css";
