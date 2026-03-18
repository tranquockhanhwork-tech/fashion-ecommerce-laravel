import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const appUrls = window.appConfig?.urls || {};

    /* ===== NAVBAR SCROLL ===== */
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    /* ===== MOBILE MENU ===== */
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu    = document.getElementById('mobile-menu');
    const mobileClose   = document.getElementById('mobile-menu-close');

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenu?.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    const closeMobile = () => {
        mobileMenu?.classList.remove('open');
        document.body.style.overflow = '';
    };

    mobileClose?.addEventListener('click', closeMobile);

    /* ===== CART SIDEBAR ===== */
    const cartOverlay = document.getElementById('cart-overlay');
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartOpenBtns = document.querySelectorAll('[data-cart-open]');
    const cartCloseBtns = document.querySelectorAll('[data-cart-close]');

    const openCart = () => {
        cartOverlay?.classList.add('open');
        cartSidebar?.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    const closeCart = () => {
        cartOverlay?.classList.remove('open');
        cartSidebar?.classList.remove('open');
        document.body.style.overflow = '';
    };

    cartOpenBtns.forEach(btn => btn.addEventListener('click', openCart));
    cartCloseBtns.forEach(btn => btn.addEventListener('click', closeCart));
    cartOverlay?.addEventListener('click', closeCart);

    /* ===== TOAST ===== */
    window.showToast = function(message, type = 'success') {
        const icons = { success: '✓', error: '✕', info: 'ℹ' };
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<span style="color:#C5A572;font-size:1.1rem;">${icons[type] || icons.success}</span> <span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 50);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    };

    const setCartCount = (count = 0) => {
        const next = Math.max(0, parseInt(count, 10) || 0);
        document.querySelectorAll('[data-cart-count]').forEach((badge) => {
            badge.textContent = next;
            badge.style.display = next > 0 ? 'flex' : 'none';
        });

        document.querySelectorAll('[data-cart-sidebar-count]').forEach((label) => {
            label.textContent = `(${next})`;
        });

        document.querySelectorAll('[data-cart-page-count]').forEach((label) => {
            label.textContent = next;
        });
    };

    const setCartTotals = (formattedTotal = '0₫') => {
        document.querySelectorAll('[data-cart-sidebar-total]').forEach((node) => {
            node.textContent = formattedTotal;
        });

        document.querySelectorAll('[data-cart-page-subtotal], [data-cart-page-total]').forEach((node) => {
            node.textContent = formattedTotal;
        });
    };

    const toggleCheckoutActions = (hasItems) => {
        document.querySelectorAll('[data-cart-checkout-action]').forEach((link) => {
            link.classList.toggle('opacity-50', !hasItems);
            link.classList.toggle('pointer-events-none', !hasItems);
        });
    };

    const renderCartEmptyState = (containerSelector, templateSelector) => {
        const container = document.querySelector(containerSelector);
        const template = document.querySelector(templateSelector);
        if (!container || !template) return;

        container.innerHTML = template.innerHTML;
    };

    const syncCartUiAfterRemove = (payload = {}) => {
        const cartCount = parseInt(payload.cart_count || '0', 10) || 0;
        const formattedTotal = payload.cart_total_formatted || '0₫';

        setCartCount(cartCount);
        setCartTotals(formattedTotal);
        toggleCheckoutActions(cartCount > 0);

        if (cartCount === 0) {
            renderCartEmptyState('[data-cart-sidebar-items]', '#cart-sidebar-empty-template');
            renderCartEmptyState('[data-cart-page-items]', '#cart-page-empty-template');
        }
    };

    /* ===== REMOVE FROM CART ===== */
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-cart-remove]');
        if (!btn) return;

        e.preventDefault();

        const itemId = btn.dataset.cartRemove;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const removeBase = appUrls.cartRemoveBase || '/cart/remove';

        if (!itemId || !csrf) {
            showToast('Không thể xóa sản phẩm lúc này.', 'error');
            return;
        }

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');

        try {
            const res = await fetch(`${removeBase}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                showToast(data.message || 'Xóa sản phẩm thất bại.', 'error');
                return;
            }

            document.querySelectorAll(`[data-cart-item="${itemId}"]`).forEach((node) => node.remove());
            syncCartUiAfterRemove(data);
            showToast(data.message || 'Đã xóa sản phẩm khỏi giỏ hàng');
        } catch (error) {
            showToast('Đã có lỗi xảy ra khi xóa sản phẩm.', 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    /* ===== ADD TO CART ===== */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;
        e.preventDefault();

        const name      = btn.dataset.productName || 'Sản phẩm';
        const productId = btn.dataset.productId;
        const csrf      = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!productId) {
            showToast('Lỗi: nút chưa có ID sản phẩm!', 'error');
            return;
        }

        const purchaseBox = btn.closest('[data-product-purchase]');
        const qtyInput = purchaseBox?.querySelector('.qty-input') || document.querySelector('[data-qty-control] .qty-input');
        const variantInput = purchaseBox?.querySelector('[data-selected-variant-id]');
        const variantId = variantInput?.value || null;
        const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

        if (btn.dataset.requiresVariant === 'true' && !variantId) {
            showToast('Vui lòng chọn màu sắc và kích cỡ còn hàng.', 'info');
            return;
        }

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');

        fetch(appUrls.cartAdd || '/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ product_id: productId, quantity: qty, product_variant_id: variantId }),
        })
        .then(async res => {
            if (res.status === 302 || res.redirected) {
                window.location.href = appUrls.login || '/login';
                return;
            }
            const data = await res.json();
            if (data.success) {
                showToast(`"${name}" đã thêm vào giỏ hàng!`);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Không thêm được vào giỏ', 'error');
            }
        })
        .catch(() => showToast('Đã có lỗi xảy ra khi kết nối', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    });

    /* ===== WISHLIST ===== */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-wishlist]');
        if (!btn) return;
        const icon = btn.querySelector('svg, span');
        btn.classList.toggle('active');
        const isActive = btn.classList.contains('active');
        btn.style.color = isActive ? '#C5A572' : '';
        showToast(isActive ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích!');
    });

    /* ===== SIZE SELECTOR ===== */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.size-btn');
        if (!btn) return;
        const group = btn.closest('[data-size-group]');
        group?.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });

    /* ===== COLOR SWATCH ===== */
    document.addEventListener('click', (e) => {
        const swatch = e.target.closest('.color-swatch');
        if (!swatch) return;
        const group = swatch.closest('[data-color-group]');
        group?.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        swatch.classList.add('active');
    });

    /* ===== QTY CONTROL ===== */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-qty]');
        if (!btn) return;
        const input = btn.closest('[data-qty-control]')?.querySelector('.qty-input');
        if (!input) return;
        const val = parseInt(input.value) || 1;
        const action = btn.dataset.qty;
        if (action === 'inc') input.value = val + 1;
        if (action === 'dec' && val > 1) input.value = val - 1;
    });

    /* ===== FILTER TOGGLE (mobile) ===== */
    const filterToggle = document.getElementById('filter-toggle');
    const filterSidebar = document.getElementById('filter-sidebar');
    filterToggle?.addEventListener('click', () => {
        filterSidebar?.classList.toggle('hidden');
    });

    /* ===== COLLAPSIBLE FILTER GROUPS ===== */
    document.querySelectorAll('.filter-title[data-collapse]').forEach(title => {
        title.addEventListener('click', () => {
            const target = document.querySelector(title.dataset.collapse);
            target?.classList.toggle('hidden');
            const icon = title.querySelector('[data-chevron]');
            if (icon) icon.style.transform = target?.classList.contains('hidden') ? 'rotate(-90deg)' : '';
        });
    });

    /* ===== SCROLL REVEAL ===== */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));

    /* ===== PRODUCT IMAGE GALLERY ===== */
    document.querySelectorAll('[data-gallery-thumb]').forEach(thumb => {
        thumb.addEventListener('click', () => {
            const mainImg = document.querySelector('[data-gallery-main]');
            if (mainImg) mainImg.src = thumb.src;
            document.querySelectorAll('[data-gallery-thumb]').forEach(t => {
                t.style.borderColor = t === thumb ? '#C5A572' : '#2a2a2a';
            });
        });
    });

    /* ===== PRODUCT VARIANT SELECTOR ===== */
    const productDetail = document.querySelector('[data-product-detail]');
    if (productDetail) {
        let variants = [];

        try {
            variants = JSON.parse(productDetail.dataset.variants || '[]');
        } catch (error) {
            variants = [];
        }

        const totalStock = parseInt(productDetail.dataset.totalStock || '0', 10);
        const colorButtons = Array.from(productDetail.querySelectorAll('[data-variant-color]'));
        const sizeButtons = Array.from(productDetail.querySelectorAll('[data-variant-size]'));
        const selectedColorLabel = productDetail.querySelector('#selected-color');
        const selectedSizeLabel = productDetail.querySelector('#selected-size');
        const stockStatus = productDetail.querySelector('#product-stock-status');
        const stockMessage = productDetail.querySelector('#variant-stock-message');
        const purchaseBox = productDetail.querySelector('[data-product-purchase]');
        const variantInput = purchaseBox?.querySelector('[data-selected-variant-id]');
        const addToCartBtn = purchaseBox?.querySelector('[data-add-to-cart]');
        const buyNowBtn = productDetail.querySelector('[data-buy-now]');

        let selectedColor = null;
        let selectedSize = null;

        const hasAvailableVariant = ({ color = null, size = null } = {}) => {
            return variants.some((variant) => {
                return (color === null || variant.color === color)
                    && (size === null || variant.size === size)
                    && variant.stock_quantity > 0;
            });
        };

        const findVariant = (color, size) => {
            return variants.find((variant) => variant.color === color && variant.size === size) || null;
        };

        const setPurchaseState = (enabled) => {
            [addToCartBtn, buyNowBtn].forEach((button) => {
                if (!button || button.tagName === 'A') return;
                button.disabled = !enabled;
                button.classList.toggle('opacity-50', !enabled);
                button.classList.toggle('cursor-not-allowed', !enabled);
            });
        };

        const updateOptionState = (buttons, key, selectedValue, dependencyKey, dependencyValue) => {
            buttons.forEach((button) => {
                const optionValue = button.dataset[key];
                const enabled = hasAvailableVariant({
                    [key.replace('variant', '').toLowerCase()]: optionValue,
                    [dependencyKey.replace('variant', '').toLowerCase()]: dependencyValue,
                });

                button.disabled = !enabled;
                button.classList.toggle('variant-option-disabled', !enabled);
                button.classList.toggle('variant-option-active', enabled && selectedValue === optionValue);
            });
        };

        const renderSelection = () => {
            updateOptionState(colorButtons, 'variantColor', selectedColor, 'variantSize', selectedSize);
            updateOptionState(sizeButtons, 'variantSize', selectedSize, 'variantColor', selectedColor);

            if (selectedColorLabel) {
                selectedColorLabel.textContent = selectedColor || 'Chưa chọn';
            }

            if (selectedSizeLabel) {
                selectedSizeLabel.textContent = selectedSize || 'Chưa chọn';
            }

            const selectedVariant = selectedColor && selectedSize
                ? findVariant(selectedColor, selectedSize)
                : null;

            if (variantInput) {
                variantInput.value = selectedVariant && selectedVariant.stock_quantity > 0 ? selectedVariant.id : '';
            }

            if (stockStatus) {
                if (selectedVariant && selectedVariant.stock_quantity > 0) {
                    stockStatus.textContent = `● Còn ${selectedVariant.stock_quantity} sản phẩm`;
                    stockStatus.className = 'text-green-400 text-sm font-medium';
                } else if (selectedColor && selectedSize) {
                    stockStatus.textContent = '● Hết hàng';
                    stockStatus.className = 'text-red-400 text-sm font-medium';
                } else if (totalStock > 0) {
                    stockStatus.textContent = '● Còn hàng';
                    stockStatus.className = 'text-green-400 text-sm font-medium';
                } else {
                    stockStatus.textContent = '● Hết hàng';
                    stockStatus.className = 'text-red-400 text-sm font-medium';
                }
            }

            if (stockMessage) {
                if (totalStock < 1) {
                    stockMessage.textContent = 'Sản phẩm hiện đã hết hàng.';
                } else if (!selectedColor || !selectedSize) {
                    stockMessage.textContent = 'Chọn màu sắc và kích cỡ để xem số lượng còn.';
                } else if (selectedVariant && selectedVariant.stock_quantity > 0) {
                    stockMessage.textContent = `Còn ${selectedVariant.stock_quantity} sản phẩm cho biến thể này.`;
                } else {
                    stockMessage.textContent = 'Biến thể này đã hết hàng.';
                }
            }

            setPurchaseState(Boolean(selectedVariant && selectedVariant.stock_quantity > 0));
        };

        colorButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (button.disabled) return;
                const nextColor = button.dataset.variantColor;
                selectedColor = selectedColor === nextColor ? null : nextColor;

                if (selectedSize && !hasAvailableVariant({ color: selectedColor, size: selectedSize })) {
                    selectedSize = null;
                }

                renderSelection();
            });
        });

        sizeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (button.disabled) return;
                const nextSize = button.dataset.variantSize;
                selectedSize = selectedSize === nextSize ? null : nextSize;

                if (selectedColor && !hasAvailableVariant({ color: selectedColor, size: selectedSize })) {
                    selectedColor = null;
                }

                renderSelection();
            });
        });

        renderSelection();
    }

    /* ===== NEWSLETTER FORM ===== */
    document.getElementById('newsletter-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        showToast('Cảm ơn bạn đã đăng ký nhận tin!');
        e.target.reset();
    });

});
