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
            <td><input type="text" name="items[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan transaksi" required></td>
            <td><input type="text" name="items[${rowIndex}][jumlah]" class="form-control form-control-sm text-end jumlah-input" placeholder="0" required></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        reindex();
        bindJumlahInputs();
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(tr).find('.select2-perkiraan').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih --', allowClear: true, width: '100%' });
        }
    });

    tbody.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        if (tbody.querySelectorAll('.item-row').length <= 1) { alert('Minimal harus ada 1 baris detail transaksi.'); return; }
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

    function terbilang(angka) {
        angka = Math.abs(parseInt(angka)) || 0;
        const s = ['','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
        if (angka === 0) return 'Nol';
        if (angka < 12) return s[angka];
        if (angka < 20) return s[angka-10]+' Belas';
        if (angka < 100) return s[Math.floor(angka/10)]+' Puluh '+terbilang(angka%10);
        if (angka < 200) return 'Seratus '+terbilang(angka-100);
        if (angka < 1000) return s[Math.floor(angka/100)]+' Ratus '+terbilang(angka%100);
        if (angka < 2000) return 'Seribu '+terbilang(angka-1000);
        if (angka < 1e6) return terbilang(Math.floor(angka/1000))+' Ribu '+terbilang(angka%1000);
        if (angka < 1e9) return terbilang(Math.floor(angka/1e6))+' Juta '+terbilang(angka%1e6);
        if (angka < 1e12) return terbilang(Math.floor(angka/1e9))+' Miliar '+terbilang(angka%1e9);
        return terbilang(Math.floor(angka/1e12))+' Triliun '+terbilang(angka%1e12);
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.jumlah-input').forEach(input => { total += parseInt(input.value.replace(/\./g, '')) || 0; });
        document.getElementById('grandTotal').textContent = 'Rp ' + formatRupiah(total);
        document.getElementById('terbilangDisplay').textContent = total > 0 ? terbilang(total).replace(/\s+/g,' ').trim()+' Rupiah' : '-';
    }

    function bindJumlahInputs() {
        document.querySelectorAll('.jumlah-input').forEach(input => {
            if (input.dataset.bound) return;
            input.dataset.bound = '1';
            input.addEventListener('input', function() { this.value = formatRupiah(this.value.replace(/\D/g, '')); hitungTotal(); });
            input.addEventListener('keypress', function(e) { if (!/\d/.test(e.key) && !['Backspace','Delete','Tab'].includes(e.key)) e.preventDefault(); });
        });
    }

    bindJumlahInputs();
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
