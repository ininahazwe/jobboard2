import './styles/app.scss';
import 'datatables.net';
import 'datatables.net-bs4';
import './bootstrap';
import Filter from './modules/Filter'

$(document).ready( function () {
    $('#datatable').DataTable();
});

new Filter(document.querySelector('.js-filter'))


// Font Awesome
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');