
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-dt/css/dataTables.dataTables.css';

window.$ = window.jQuery = $;

$(function () {
    $('#hbo-table').DataTable({
        pageLength: 10,
        lengthChange: false,
        searching: true,
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search items..."
        },
        initComplete: function () {
            // Search box styling
            $('#hbo-table_filter input')
                .addClass('border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none')
                .css('width', '250px');

            // Position search bar
            $('#hbo-table_filter').addClass('mb-4 float-left');

            // Pagination styling
            $('.dataTables_paginate').addClass('flex justify-end mt-4');
            $('.dataTables_paginate a')
                .addClass('px-3 py-1 border border-gray-300 rounded mx-1 text-gray-700 hover:bg-green-500 hover:text-white transition');
        }
    });
});