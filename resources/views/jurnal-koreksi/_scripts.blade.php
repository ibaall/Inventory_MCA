<script>
(function() {
    let rowIndex = {{ $rowCount ?? 1 }};
    const tbody = document.getElementById('itemsBody');
    const perkiraanOptions = `<option value="">-- Pilih --</option>@foreach(\App\Models\NoPerkiraan::orderBy('kode_perkiraan')->get() as $p)<option value="{{ $p->id }}" data-nama="{{ $p->nama_perkiraan }}">{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>@endforeach`;

    document.getElementById('addRow').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.classList.add('item-row');
        tr.innerHTML = `
            <td class="row-number text-center align-middle fw-semibold">${rowIndex + 1}</td>
            <td><select name="items[${rowIndex}][no_perkiraan_id]" class="form-select form-select-sm select2-perkiraan" onchange="fillNamaPerkiraan(this)">${perkiraanOptions}</select></td>
            <td><input type="text" name="items[${rowIndex}][nama_perkiraan]" class="form-control form-control-sm nama-perkiraan-input" readonly placeholder="Otomatis"></td>
            <td><input type="text" name="items[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan detail"></td>
            <td><input type="text" name="items[${rowIndex}][debit]" class="form-control form-control-sm text-end debit-input" placeholder="0"></td>
            <td><input type="text" name="items[${rowIndex}][kredit]" class="form-control form-control-sm text-end kredit-input" placeholder="0"></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        reindex();
        bindInputs();
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(tr).find('.select2-perkiraan').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih --', allowClear: true, width: '100%' });
        }
    });

    tbody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        if (tbody.querySelectorAll('.item-row').length <= 1) { alert('Minimal harus ada 1 baris detail.'); return; }
        const row = btn.closest('tr');
        const sel = row.querySelector('.select2-perkiraan');
        if (sel && typeof $ !== 'undefined' && $(sel).data('select2')) $(sel).select2('destroy');
        row.remove();
        reindex();
        hitungTotal();
    });

    function reindex() { tbody.querySelectorAll('.item-row').forEach((row, i) => { row.querySelector('.row-number').textContent = i + 1; }); }

    function formatRupiah(val) {
        const num = val.toString().replace(/\D/g, '');
        return num ? num.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
    }

    function hitungTotal() {
        let totalDebit = 0, totalKredit = 0;
        document.querySelectorAll('.debit-input').forEach(input => { totalDebit += parseInt(input.value.replace(/\./g, '')) || 0; });
        document.querySelectorAll('.kredit-input').forEach(input => { totalKredit += parseInt(input.value.replace(/\./g, '')) || 0; });
        document.getElementById('totalDebit').textContent = 'Rp ' + formatRupiah(totalDebit);
        document.getElementById('totalKredit').textContent = 'Rp ' + formatRupiah(totalKredit);

        const alert = document.getElementById('balanceAlert');
        if (totalDebit > 0 && totalKredit > 0 && totalDebit !== totalKredit) {
            alert.classList.remove('d-none');
        } else {
            alert.classList.add('d-none');
        }
    }

    function bindInputs() {
        document.querySelectorAll('.debit-input, .kredit-input').forEach(input => {
            if (input.dataset.bound) return;
            input.dataset.bound = '1';
            input.addEventListener('input', function() { this.value = formatRupiah(this.value.replace(/\D/g, '')); hitungTotal(); });
            input.addEventListener('keypress', function(e) { if (!/\d/.test(e.key) && !['Backspace','Delete','Tab'].includes(e.key)) e.preventDefault(); });
        });
    }

    bindInputs();
    hitungTotal();

    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-perkiraan').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih --', allowClear: true, width: '100%' });
    }
})();

function fillNamaPerkiraan(selectEl) {
    const row = selectEl.closest('tr');
    if (!row) return;
    const namaInput = row.querySelector('.nama-perkiraan-input');
    if (!namaInput) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    namaInput.value = opt && opt.dataset.nama ? opt.dataset.nama : '';
}
</script>
