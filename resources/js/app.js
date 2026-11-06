import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import TomSelect from 'tom-select';
import flatpickr from 'flatpickr';
import Swal from 'sweetalert2';

window.$ = window.jQuery = $;
window.DataTable = DataTable;
window.TomSelect = TomSelect;
window.flatpickr = flatpickr;
window.Swal = Swal;

DataTable.use($);
