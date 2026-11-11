import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */


import './styles/app.css';

import * as Turbo from '@hotwired/turbo';

// Turbo Drive (ajax page navigation) is off: every link/form does a normal page load.
// Turbo is only used for Frames (modals, dynamic form fields) and Streams (ajax_controller).
// https://turbo.hotwired.dev/handbook/drive#disabling-turbo-drive-on-specific-links-or-forms
Turbo.session.drive = false;
