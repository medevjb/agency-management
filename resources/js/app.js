import './bootstrap';

import Alpine from 'alpinejs';
import $ from 'jquery';
import jszip from 'jszip';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import Swal from 'sweetalert2/dist/sweetalert2.all.js';

import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.JSZip = jszip;
window.Swal = Swal;
pdfMake.vfs = pdfFonts.pdfMake.vfs;

// Mark that Vite assets have been processed
window.__vite_processed__ = true;

Alpine.start();
