(function () {
    function formatRupiah(value) {
        return 'Rp' + Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function calculateSubtotal(row) {
        const qty = parseInt(row.querySelector('[data-qty-input]').value || '0', 10);
        const price = parseFloat(row.querySelector('[data-price-input]').value || '0');
        return qty * price;
    }

    function updateRow(row, updateTotal) {
        const qtyInput = row.querySelector('[data-qty-input]');
        const qtyDisplay = row.querySelector('[data-qty-display]');
        const subtotalText = row.querySelector('[data-subtotal-text]');
        const subtotal = calculateSubtotal(row);

        qtyDisplay.textContent = qtyInput.value;
        subtotalText.textContent = formatRupiah(subtotal);
        updateTotal();
    }

    function bindCartRow(row, updateTotal) {
        row.querySelector('[data-remove-item]').addEventListener('click', function () {
            row.remove();
            updateTotal();
        });

        row.querySelector('[data-increase]').addEventListener('click', function () {
            const qtyInput = row.querySelector('[data-qty-input]');
            qtyInput.value = parseInt(qtyInput.value || '0', 10) + 1;
            updateRow(row, updateTotal);
        });

        row.querySelector('[data-decrease]').addEventListener('click', function () {
            const qtyInput = row.querySelector('[data-qty-input]');
            const current = parseInt(qtyInput.value || '0', 10);

            if (current <= 1) {
                row.remove();
                updateTotal();
                return;
            }

            qtyInput.value = current - 1;
            updateRow(row, updateTotal);
        });
    }

    function initRestockPage(root) {
        if (!root || !window.restockProducts) {
            return;
        }

        const catalog = Object.values(window.restockProducts);
        const searchInput = root.querySelector('[data-product-search]');
        const results = root.querySelector('[data-product-results]');
        const resultsEmpty = root.querySelector('[data-product-results-empty]');
        const cartItems = root.querySelector('[data-cart-items]');
        const cartTotalText = root.querySelector('[data-cart-total]');

        function updateTotal() {
            let total = 0;
            cartItems.querySelectorAll('.cart-item').forEach((row) => {
                total += calculateSubtotal(row);
            });

            cartTotalText.textContent = formatRupiah(total);
            const hasItems = !!cartItems.querySelector('.cart-item');
            let empty = cartItems.querySelector('[data-cart-empty]');

            if (!hasItems) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'text-muted helper-text';
                    empty.id = 'emptyCartMessage';
                    empty.setAttribute('data-cart-empty', '');
                    empty.textContent = 'Belum ada item dipilih.';
                    cartItems.appendChild(empty);
                }

                empty.style.display = 'block';
                return;
            }

            if (empty) {
                empty.style.display = 'none';
            }
        }

        function addOrIncreaseItem(productId) {
            const product = window.restockProducts[productId];
            if (!product) {
                return;
            }

            const existing = cartItems.querySelector(`.cart-item[data-product-id="${productId}"]`);
            if (existing) {
                const qtyInput = existing.querySelector('[data-qty-input]');
                qtyInput.value = parseInt(qtyInput.value || '0', 10) + 1;
                existing.querySelector('[data-price-input]').value = product.harga_beli;
                updateRow(existing, updateTotal);
                return;
            }

            const empty = cartItems.querySelector('[data-cart-empty]');
            if (empty) {
                empty.remove();
            }

            const row = document.createElement('div');
            row.className = 'cart-item';
            row.dataset.productId = productId;
            row.innerHTML = `
                <input type="hidden" name="id_produk[]" value="${productId}" data-id-input>
                <input type="hidden" name="jumlah[]" value="1" data-qty-input>
                <input type="hidden" name="harga_beli[]" value="${product.harga_beli}" data-price-input>
                <div class="cart-item-head">
                    <div>
                        <strong>${product.nama_produk}</strong>
                        <div class="cart-item-meta">${product.kode_produk ? product.kode_produk : '-'}${product.satuan ? ' · ' + product.satuan : ''}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" type="button" data-remove-item aria-label="Hapus item">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="cart-total-row">
                    <span class="cart-stepper">
                        <button class="btn btn-outline-secondary" type="button" data-decrease>-</button>
                        <strong data-qty-display>1</strong>
                        <button class="btn btn-outline-secondary" type="button" data-increase>+</button>
                    </span>
                    <strong data-subtotal-text>${formatRupiah(product.harga_beli)}</strong>
                </div>
            `;

            bindCartRow(row, updateTotal);
            cartItems.appendChild(row);
            updateTotal();
        }

        function renderResults(query) {
            const normalizedQuery = query.trim().toLowerCase();
            const filtered = catalog.filter((product) => {
                const haystack = `${product.nama_produk} ${product.kode_produk || ''}`.toLowerCase();
                return normalizedQuery === '' || haystack.includes(normalizedQuery);
            });

            results.innerHTML = '';

            if (!filtered.length) {
                resultsEmpty.style.display = 'block';
                return;
            }

            resultsEmpty.style.display = 'none';

            filtered.forEach((product) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'product-result-item';
                button.innerHTML = `
                    <span class="product-result-name">${product.nama_produk}</span>
                    <span class="product-result-price">+ ${formatRupiah(product.harga_beli)}</span>
                `;
                button.addEventListener('click', function () {
                    addOrIncreaseItem(product.id_produk);
                    searchInput.value = '';
                    renderResults('');
                    searchInput.focus();
                });
                results.appendChild(button);
            });
        }

        root.querySelectorAll('.cart-item').forEach((row) => bindCartRow(row, updateTotal));
        updateTotal();
        renderResults('');

        searchInput.addEventListener('input', function () {
            renderResults(searchInput.value);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-restock-page]').forEach(initRestockPage);
    });
})();
