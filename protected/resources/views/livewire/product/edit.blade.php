<div class="row">
    <div class="col-md-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Message!</h4>
                Data anda telah tersimpan.
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div x-data="{
        isActiveVariant: @entangle('isActiveVariant'),
        variations: @entangle('variations'),
        tableRows: @entangle('tableRows'),

        activateVariant() {
            this.isActiveVariant = true;
        },

        init() {
            {{-- this.updateTableRows(); --}}
        },

        addVariation() {
            if (this.variations.length < 2) {
                this.variations.push({ name: '', options: [''] });
                this.updateTableRows();
            } else {
                alert('Tidak dapat menambahkan lebih dari 2 variasi');
            }
        },

        removeVariation(index) {
            this.variations.splice(index, 1);
            this.updateTableRows();
        },

        addOption(vIndex) {
            this.variations[vIndex].options.push('');
            this.updateTableRows();
        },

        updateOption() {
            this.updateTableRows();
        },

        removeOption(vIndex, optionIndex) {
            this.variations[vIndex].options.splice(optionIndex, 1);
            this.updateTableRows();
        },

        updateVariationName(index, newName) {
            const oldName = this.variations[index].name;

            // Update the variation name
            this.variations[index].name = newName;

            // Update table rows with the new name
            const updatedRows = this.tableRows.map((row) => {
                const newRow = { ...row };
                if (oldName) {
                    newRow[newName.toLowerCase()] = newRow[oldName.toLowerCase()] || '';
                    delete newRow[oldName.toLowerCase()];
                }
                return newRow;
            });

            this.tableRows = updatedRows;

            // Regenerate table rows to ensure they align with the updated columns
            this.updateTableRows();
        },

        updateTableRows() {
            if (this.variations.length === 0) {
                this.tableRows = [];
                return;
            }

            // Generate the Cartesian product of all options
            const optionsList = this.variations.map((variation) => variation.options);
            const combinations = this.cartesianProduct(...optionsList);

            // Map the combinations to table rows while preserving existing values
            const updatedRows = combinations.map((combination, vIndex) => {
                // Build a key based on the combination
                const key = combination.join('|').toLowerCase();

                // Find a matching row in the current tableRows
                const existingRow = this.tableRows.find((row) => {
                    return this.variations.every((variation, index) => {
                        return row[variation.name.toLowerCase()] === combination[index];
                    });
                });

                // If a matching row exists, retain its values, otherwise create a new row
                let harga = '';
                let stok = '';
                let kode = '';

                if (this.tableRows[vIndex]?.harga) {
                    harga = this.tableRows[vIndex].harga;
                }

                if (this.tableRows[vIndex]?.stok) {
                    stok = this.tableRows[vIndex].stok;
                }

                if (this.tableRows[vIndex]?.kode) {
                    kode = this.tableRows[vIndex].kode;
                }

                return existingRow
                    ? { ...existingRow }
                    : {
                        harga: harga,
                        stok: stok,
                        kode: kode,
                        ...this.variations.reduce((acc, variation, index) => {
                            acc[variation.name.toLowerCase()] = combination[index];
                            return acc;
                        }, {})
                    };
            });

            // Update the tableRows with the newly generated rows
            this.tableRows = updatedRows;
        },

        cartesianProduct(...arrays) {
            return arrays.reduce((a, b) =>
                a.flatMap((x) => b.map((y) => [...x, y])), [
                    []
                ]);
        },

        saveProduct() {
            for (const row of this.tableRows) {
                if (!row.harga) {
                    alert('Semua kolom harga harus diisi!');
                    return false; // If any field is empty, prevent form submission
                }
            }

            this.$wire.saveProduct();
        }
    }">
        <div class="col-md-8">
            <!-- general form elements disabled -->
            <div class="box box-warning">
                <div class="box-body">
                    <div class="form-group">
                        <label for="supplier_id">Supplier</label>
                        <select class="form-control" wire:model="supplier">
                            <option hidden>Pilih supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama produk</label>
                        <input type="text" class="form-control" wire:model="productName">
                    </div>
                    <div class="form-group" x-data>
                        <label for="harga">Harga beli</label>
                        <input type="text" class="form-control money"
                        x-init="$nextTick(() => {
                            setTimeout(() => {
                                $($el).mask('000.000.000.000', {reverse: true});
                            }, 1000)
                        })"
                        wire:model="hargaBeli">
                    </div>
                    <div class="form-group" x-data>
                        <label for="harga">Harga jual</label>
                        <input type="text" class="form-control money"
                        x-init="$nextTick(() => {
                            setTimeout(() => {
                                $($el).mask('000.000.000.000', {reverse: true});
                            }, 1000)
                        })"
                        wire:model="hargaJual">
                    </div>
                </div>
            </div>

            <div class="box box-warning">
                <div class="box-body">
                    <div class="form-group">
                        <label style="margin-right: 1rem">Variasi</label>
                        <button type="button" class="btn btn-warning" @click="activateVariant"
                            x-show="!isActiveVariant" x-cloak>Aktifkan Variasi</button>
                    </div>
                    <div x-show="isActiveVariant" x-cloak>
                        <div class="">
                            <template x-for="(variation, vIndex) in variations" :key="vIndex">
                                <div class="form-group"
                                    style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Variasi <span x-text="vIndex + 1"></span></label>
                                            <input type="text" class="form-control" placeholder="Ketik atau pilih"
                                                maxlength="14" x-model="variation.name"
                                                @input="updateVariationName(vIndex, $event.target.value)">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Opsi</label>
                                            <template x-for="(option, oIndex) in variation.options"
                                                :key="oIndex">
                                                <div class="input-group" style="margin-bottom: 5px;">
                                                    <input type="text" class="form-control"
                                                        placeholder="Cth. Merah, dll" maxlength="20"
                                                        x-model="variation.options[oIndex]" @input="updateOption">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-danger btn-sm"
                                                            @click.prevent="removeOption(vIndex, oIndex)">
                                                            <i class="glyphicon glyphicon-trash"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </template>
                                            <button class="btn btn-success btn-sm" @click.prevent="addOption(vIndex)">
                                                <i class="glyphicon glyphicon-plus"></i> Tambah Opsi
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right" style="margin-top: 10px;">
                                        <button class="btn btn-danger btn-sm" @click.prevent="removeVariation(vIndex)">
                                            <i class="glyphicon glyphicon-trash"></i> Hapus Variasi
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <button class="btn btn-default btn-block" @click.prevent="addVariation">
                                <i class="glyphicon glyphicon-plus"></i> Tambah Variasi
                            </button>
                            <!-- Generated Table -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div style="margin-top: 1rem">
                                        <div style="display: inline-flex;align-items: center;"
                                            x-data="{
                                                allHarga: null,
                                                applyHarga() {
                                                    this.tableRows.forEach((row) => {
                                                        row.harga = this.allHarga
                                                    })
                                                }
                                            }"
                                        >
                                            <label for="sku" style="margin-right: 1rem">Harga</label>
                                            <input type="number" class="form-control" x-model="allHarga">
                                            <button class="btn btn-primary" @click="applyHarga">Terapkan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="margin-top: 1rem">
                                        <div style="display: inline-flex;align-items: center;"
                                            x-data="{
                                                allSku: null,
                                                applySku() {
                                                    this.tableRows.forEach((row) => {
                                                        row.kode = this.allSku
                                                    })
                                                }
                                            }"
                                        >
                                            <label for="sku" style="margin-right: 1rem">SKU</label>
                                            <input type="text" class="form-control" x-model="allSku">
                                            <button class="btn btn-primary" @click="applySku">Terapkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive" style="margin-top: 20px;" x-show="variations.length > 0">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <template x-for="variation in variations" :key="variation.name">
                                                <th x-text="variation.name"></th>
                                            </template>
                                            <th>Harga User</th>
                                            <th>Stok</th>
                                            <th>SKU / Kode Variasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, idx) in tableRows" :key="idx">
                                            <tr>
                                                <template x-for="variation in variations" :key="variation.name">
                                                    <td x-text="row[variation.name.toLowerCase()].toUpperCase()"></td>
                                                </template>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        placeholder="Masukkan harga" x-model="row.harga">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        placeholder="Masukkan stok" x-model="row.stok">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        placeholder="Masukkan kode" x-model="row.kode">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary pull-right"
                    @click.prevent="saveProduct">Simpan</button>
            </div>
        </div>
    </div>
</div>
