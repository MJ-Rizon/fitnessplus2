// filepath: c:\xampp\htdocs\Fitness\admin\js\searchMembers.js
document.getElementById('memberSearch').addEventListener('keyup', function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll('.data-table tbody tr');
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});