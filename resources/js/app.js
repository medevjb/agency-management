import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
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

window.$ = window.jQuery = $;
window.JSZip = jszip;
window.Swal = Swal;

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// Safely initialize pdfMake with fonts
if (pdfMake && pdfFonts && pdfFonts.pdfMake && pdfFonts.pdfMake.vfs) {
    pdfMake.vfs = pdfFonts.pdfMake.vfs;
    window.pdfMake = pdfMake;
} else {
    console.warn('PDFMake fonts not properly initialized');
}
