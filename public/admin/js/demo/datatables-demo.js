// function initDataTable() {
//   $('#dataTable').DataTable({
//     destroy: true // VERY IMPORTANT: destroy old instance
//   });
// }

function initDataTable() {
    $('#dataTable').DataTable({
        destroy: true,
        lengthMenu: [ [10, 25, 50, 100, 500], [10, 25, 50, 100, 500] ]
    });
}
